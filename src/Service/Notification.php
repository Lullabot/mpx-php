<?php

/**
 * @file
 * Contains Mpx\NotificationService.
 */

namespace Mpx\Service;

use GuzzleHttp\Url;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use Mpx\Exception\ApiException;
use Mpx\Exception\NotificationExpiredException;
use Mpx\ClientTrait;
use Mpx\ClientInterface;
use Mpx\LoggerTrait;
use Mpx\UserInterface;

class Notification implements NotificationInterface {
  use ClientTrait;
  use LoggerTrait;

  /** @var \Mpx\UserInterface */
  protected $user;

  /** @var \GuzzleHttp\Url */
  protected $uri;

  /** @var string */
  protected $lastId;

  /**
   * Construct an mpx notification service.
   *
   * @param \GuzzleHttp\Url|string $uri
   * @param \Mpx\UserInterface $user
   * @param string $lastId
   * @param \Mpx\ClientInterface $client
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct($uri, UserInterface $user, $lastId = NULL, ClientInterface $client = NULL, LoggerInterface $logger = NULL) {
    $this->uri = is_string($uri) ? Url::fromString($uri) : $uri;
    $this->user = $user;
    $this->lastId = $lastId;
    $this->client = $client;
    $this->logger = $logger;
  }

  /**
   * Create a new instance of a notification service class.
   *
   * @param \GuzzleHttp\Url|string $uri
   * @param \Mpx\UserInterface $user
   * @param string $lastId
   * @param \Pimple\Container $container
   *
   * @return static
   */
  public static function create($uri, UserInterface $user, $lastId, Container $container) {
    return new static(
      $uri,
      $user,
      $lastId,
      $container['client'],
      $container['logger']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setLastId($value) {
    $this->lastId = $value;
    $this->logger()->info(
      'Set the last notification sequence ID: {value}',
      array(
        'value' => var_export($value, TRUE),
      )
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getLastId() {
    return $this->lastId;
  }

  /**
   * {@inheritdoc}
   */
  public function syncLatestId(array $options = []) {
    $data = $this->client()->authenticatedGet($this->user, $this->uri, $options);

    $last_id = NULL;
    // Only care about the first notification in the data.
    $this->processNotifications(array_slice($data, 0, 1), $last_id);

    if (empty($last_id)) {
      throw new \Exception("Unable to fetch the latest notification sequence ID from {$this->uri} for {$this->user}.");
    }

    $this->logger()->info(
      "Fetched the latest notification sequence ID from {url}: {value}",
      array(
        'value' => var_export($data[0]['id'], TRUE),
        'url' => $this->uri,
      )
    );

    $this->setLastId($last_id);

    return $last_id;
  }

  /**
   * {@inheritdoc}
   */
  public function fetch($limit = 500, array $options = []) {
    $last_id = $this->getLastId();
    if (!$last_id) {
      throw new \LogicException("Cannot call " . __METHOD__ . " when the last notification ID is empty.");
    }

    $notifications = array();

    $options += array(
      'query' => array(),
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

        $options['query']['since'] = $last_id;
        $data = $this->client()->authenticatedGet(
          $this->user,
          $this->uri,
          $options
        );

        // Process the notifications.
        $notifications = $this->processNotifications($data, $last_id, $notifications);

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
          $this->setLastId(NULL);
          throw new NotificationExpiredException("The notification sequence ID {$last_id} is older than 7 days and is too old to fetch notifications.");
        }
        else {
          throw $exception;
        }
      }
    } while (count($data) == $options['query']['size']);

    $this->logger()->info(
      'Fetched {count} notifications from {url} for {account}.',
      array(
        'count' => count($notifications),
        'url' => $this->uri,
        'account' => $this->user->getUsername(),
      )
    );

    // Set the last notification ID once all the requests have completed.
    $this->setLastId($last_id);

    $this->logger()->debug(
      "Notification data: {data}",
      array(
        'data' => print_r($notifications, TRUE),
      )
    );

    return $notifications;
  }

  /**
   * {@inheritdoc}
   */
  public function listen(array $options = []) {
    $last_id = $this->getLastId();
    if (!$last_id) {
      throw new \LogicException("Cannot call " . __METHOD__ . " when the last notification ID is empty.");
    }

    $options['query']['since'] = $last_id;
    $options['query']['block'] = 'true';

    $this->logger()->info(
      'Listening for notifications from {url} for {account}.',
      array(
        'url' => $this->uri,
        'account' => $this->user->getUsername(),
      )
    );

    try {
      $data = $this->client()->authenticatedGet(
        $this->user,
        $this->uri,
        $options
      );
    }
    catch (ApiException $exception) {
      // A 404 response means the notification ID that we have is now older than
      // 7 days, and now we have to start ingesting from the beginning again.
      if ($exception->getCode() == 404) {
        $this->setLastId(NULL);
        throw new NotificationExpiredException("The notification sequence ID {$last_id} is older than 7 days and is too old to fetch notifications.");
      }
      else {
        throw $exception;
      }
    }

    // Process the notifications.
    $notifications = $this->processNotifications($data, $last_id);

    $this->logger()->info(
      'Fetched {count} notifications from {url} for {account}.',
      array(
        'count' => count($notifications),
        'url' => $this->uri,
        'account' => $this->user->getUsername(),
      )
    );

    // Set the last notification ID once all the requests have completed.
    $this->setLastId($last_id);

    $this->logger()->debug(
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
   * @param int &$last_id
   *   The current last ID, to be updated from the notification data.
   * @param array $notifications
   *   An optional array to which to add the existing notifications.
   *
   * @return array
   *   An array of notifications.
   */
  private function processNotifications(array $data, &$last_id, array $notifications = array()) {
    foreach ($data as $notification) {
      // Update the most recently seen notification ID.
      if (!empty($notification['id'])) {
        $last_id = $notification['id'];
      }

      if (!empty($notification['entry'])) {
        // @todo Convert these to a notification class?
        $notifications[] = array(
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
}
