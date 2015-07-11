<?php

/**
 * @file
 * Contains Mpx\NotificationService.
 */

namespace Mpx\Service;

use GuzzleHttp\Psr7\Uri;
use Mpx\Exception\ApiException;
use Mpx\Exception\NotificationExpiredException;
use Mpx\ClientInterface;
use Mpx\HasCachePoolTrait;
use Mpx\HasClientTrait;
use Mpx\HasLoggerTrait;
use Mpx\UserInterface;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use Stash\Interfaces\PoolInterface;
use GuzzleHttp\Event\HasEmitterTrait;
use Mpx\Event\NotificationEvent;

class NotificationService implements NotificationServiceInterface {
  use HasCachePoolTrait;
  use HasClientTrait;
  use HasEmitterTrait;
  use HasLoggerTrait;

  /** @var \Mpx\UserInterface */
  protected $user;

  /** @var \Psr\Http\Message\UriInterface */
  protected $uri;

  /** @var \Stash\Interfaces\ItemInterface */
  private $idCache;

  /**
   * Construct an mpx notification service.
   *
   * @param \Psr\Http\Message\UriInterface|string $uri
   * @param \Mpx\UserInterface $user
   * @param \Mpx\ClientInterface $client
   * @param \Stash\Interfaces\PoolInterface $cache
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct($uri, UserInterface $user, ClientInterface $client = NULL, PoolInterface $cache = NULL, LoggerInterface $logger = NULL) {
    $this->uri = is_string($uri) ? new Uri($uri) : $uri;
    $this->user = $user;
    $this->client = $client;
    $this->cachePool = $cache;
    $this->logger = $logger;

    $this->idCache = $this->getCachePool()->getItem($this->getCacheKey());
  }

  /**
   * Create a new instance of a notification service class.
   *
   * @param \Psr\Http\Message\UriInterface|string $uri
   * @param \Mpx\UserInterface $user
   * @param \Pimple\Container $container
   *
   * @return static
   */
  public static function create($uri, UserInterface $user, Container $container) {
    return new static(
      $uri,
      $user,
      $container['client'],
      $container['cache'],
      $container['logger']
    );
  }

  private function getCacheKey() {
    $cache_uri = clone $this->getUri();

    // Filter out query parameters that should not affect the cache key.
    $query = $cache_uri->getQuery();
    parse_str($query, $query);
    /** @var array $query */
    foreach (array_keys($query) as $key) {
      if (!in_array($key, array('filter', 'account'))) {
        $cache_uri = $cache_uri::withoutQueryValue($cache_uri, $key);
      }
    }
    return 'notification:' . md5($this->getUser()->getUsername() . ':' . $cache_uri);
  }

  /**
   * {@inheritdoc}
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * {@inheritdoc}
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastId() {
    return $this->idCache->get();
  }

  /**
   * {@inheritdoc}
   */
  public function setLastId($value) {
    $this->idCache->set($value);
    $this->getLogger()->notice(
      'Set the last notification sequence ID: {value}',
      array(
        'value' => var_export($value, TRUE),
      )
    );
  }

  /**
   * {@inheritdoc}
   */
  public function fetchLatestId(array $options = []) {
    // Only care about the first notification in the data.
    $options['query']['size'] = 1;

    $data = $this->getClient()->authenticatedGet($this->user, $this->uri, $options);

    if (empty($data[0]['id'])) {
      throw new \Exception("Unable to fetch the latest notification sequence ID from {$this->uri} for {$this->getUser()->getUsername()}.");
    }

    $this->getLogger()->info(
      "Fetched the latest notification sequence ID from {url}: {value}",
      array(
        'value' => var_export($data[0]['id'], TRUE),
        'url' => $this->uri,
      )
    );

    return $data[0]['id'];
  }

  /**
   * {@inheritdoc}
   */
  public function fetch($limit = 500, array $options = []) {
    $lastId = $this->getLastId();
    if (!$lastId) {
      throw new \LogicException("Cannot call " . __METHOD__ . " when the last notification ID is empty.");
    }

    $notifications = array();

    $options += array(
      'query' => array(),
    );

    $real_uri = $this->uri;
    foreach ($options['query'] as $key => $value) {
      $real_uri = $real_uri::withQueryValue($real_uri, $key, $value);
    }
    $this->getLogger()->info(
      'Starting to request notifications from {url} for {account}.',
      array(
        'url' => $real_uri,
        'account' => $this->user->getUsername(),
      )
    );

    $options['query'] += array(
      'size' => 500,
    );

    // If $limit === 0, set it to NULL.
    if (!$limit) {
      $limit = NULL;
    }

    do {
      try {
        if ($limit) {
          // Don't request more than we need.
          $options['query']['size'] = min($options['query']['size'], $limit);
        }

        $options['query']['since'] = $lastId;
        $data = $this->getClient()->authenticatedGet(
          $this->user,
          $this->uri,
          $options
        );

        // Process the notification data.
        $notifications += $this->processNotificationData($data);
        if (!empty($notifications)) {
          $lastId = max(array_keys($notifications));
        }

        if ($limit) {
          $limit -= count($data);
          if ($limit <= 0) {
            // Break out of the do/while loop since we have fetched the
            // desired number of notifications.
            break;
          }
        }
      }
      catch (ApiException $exception) {
        // A 404 response means the notification ID that we have is now older than
        // 7 days, and now we have to start ingesting from the beginning again.
        if ($exception->getCode() == 404) {
          throw new NotificationExpiredException("The notification sequence ID {$lastId} is older than 7 days and is too old to fetch notifications.");
        }
        else {
          throw $exception;
        }
      }
    } while (count($data) == $options['query']['size']);

    $this->getLogger()->info(
      'Fetched {count} notifications from {url} for {account}.',
      array(
        'count' => count($notifications),
        'url' => $real_uri,
        'account' => $this->user->getUsername(),
      )
    );

    $this->getLogger()->debug(
      "Notification data: {data}",
      array(
        'data' => print_r($notifications, TRUE),
      )
    );

    return $notifications;
  }

  /**
   * Process notification data from the API.
   *
   * @param array $data
   *   The raw data from the API.
   *
   * @return array
   *   An array of notifications.
   */
  protected function processNotificationData(array $data) {
    $notifications = array();

    foreach ($data as $notification) {
      if (!empty($notification['id'])) {
        $notifications[$notification['id']] = array();
      }

      if (!empty($notification['entry'])) {
        // @todo Convert these to a notification class?
        $notifications[$notification['id']] = array(
          'type' => $notification['type'],
          // The ID is always a fully qualified URI, and we only care about the
          // actual ID value, which is at the end.
          'id' => basename($notification['entry']['id']),
          'method' => $notification['method'],
          'updated' => $notification['entry']['updated']
        );
      }
    }

    return $notifications;
  }

  /**
   * {@inheritdoc}
   */
  public function syncLatestId($options = []) {
    $latest = $this->fetchLatestId($options);
    if ($latest != $this->getLastId()) {
      $this->processNotificationReset($latest);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function readNotifications($limit = 500, $options = []) {
    if (!$this->getLastId()) {
      $latest = $this->fetchLatestId();
      $this->processNotificationReset($latest);
    }
    else {
      try {
        if ($notifications = $this->fetch($limit, $options)) {
          $this->getEmitter()->emit('notify', new NotificationEvent($this, $notifications));
          $this->processNotifications($notifications);
        }
      }
      catch (NotificationExpiredException $exception) {
        $this->processNotificationReset(NULL);
        throw $exception;
      }
    }
  }

  /**
   * Process the most recent notifications.
   *
   * @param array $notifications
   *   The array of notifications keyed by notification ID.
   */
  protected function processNotifications(array $notifications) {
    $this->setLastId(max(array_keys($notifications)));
  }

  /**
   * Process the notification ID being reset due to missing notifications.
   *
   * @param string $id
   *   The reset notification ID value.
   */
  protected function processNotificationReset($id) {
    $this->setLastId($id);
  }
}
