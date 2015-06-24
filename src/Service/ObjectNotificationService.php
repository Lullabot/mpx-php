<?php

namespace Mpx\Service;

use Mpx\ClientInterface;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use Stash\Interfaces\PoolInterface;

class ObjectNotificationService extends NotificationService {

  /** @var \Mpx\Service\ObjectServiceInterface */
  protected $objectService;

  /**
   * Construct an mpx object notification service.
   *
   * @param \GuzzleHttp\Url|string $uri
   * @param \Mpx\Service\ObjectServiceInterface $objectService
   * @param \Mpx\ClientInterface $client
   * @param \Stash\Interfaces\PoolInterface $cache
   * @param \Psr\Log\LoggerInterface $logger
   *
   * @throws \Exception
   */
  public function __construct($uri, ObjectServiceInterface $objectService, ClientInterface $client = NULL, PoolInterface $cache = NULL, LoggerInterface $logger = NULL) {
    parent::__construct(
      $uri,
      $objectService->getUser(),
      $client,
      $cache,
      $logger
    );
    $this->objectService = $objectService;
  }

  /**
   * Create a new instance of a notification service class.
   *
   * @return static
   */
  public static function create($uri, ObjectServiceInterface $objectService, Container $container) {
    return new static(
      $uri,
      $objectService,
      $container['client'],
      $container['cache'],
      $container['logger']
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function processNotifications(array $notifications) {
    $ids = array();
    foreach ($notifications as $notification_id => $notification) {
      if (!empty($notification['id'])) {
        $ids[] = $notification['id'];
      }
    }

    if ($ids = array_unique($ids)) {
      $this->objectService->resetCache($ids);

      $objects = $this->objectService->loadMultiple($ids);
      foreach ($objects as $object) {
        $this->logger()->info($object);
      }
    }

    parent::processNotifications($notifications);
  }

  /**
   * {@inheritdoc}
   */
  protected function processNotificationReset($id) {
    $this->objectService->resetCache();

    parent::processNotificationReset($id);
  }

}
