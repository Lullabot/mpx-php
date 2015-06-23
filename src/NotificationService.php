<?php

/**
 * @file
 * Contains Mpx\NotificationService.
 */

namespace Mpx;

use GuzzleHttp\Url;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use Mpx\Exception\ApiException;
use Mpx\Exception\NotificationExpiredException;

class NotificationService implements NotificationServiceInterface {
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
  public function fetchLatestId(array $options = []) {
    $options['query']['since'] = '-1';
    $data = $this->client()->authenticatedGet($this->user, $this->uri, $options);

    if (!isset($data[0]['id'])) {
      throw new \Exception("Unable to fetch the latest notification sequence ID from {$this->uri} for {$this->user}.");
    }
    elseif (!is_numeric($data[0]['id'])) {
      throw new \UnexpectedValueException("The latest notification sequence ID {$data[0]['id']} from {$this->uri} for {$this->user} was not a numeric value.");
    }

    $this->logger()->info(
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
  public function fetchNotifications($limit = 500, array $options = []) {
    $last_id = $this->getLastId();
    if (!$last_id) {
      throw new \LogicException("Cannot call " . __METHOD__ . " when the last notification ID is empty.");
    }

    $notifications = array();

    $options += array(
      'query' => array(),
    );
    $options['query'] += array(
      'block' => 'false',
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

          if ($limit) {
            $limit--;
            if (!$limit) {
              // Break out of the do/while loop since we have fetched the
              // desired number of notifications.
              break 2;
            }
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
}
