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
   * @param \Mpx\Service\ObjectServiceInterface $objectService
   * @param \Mpx\ClientInterface $client
   * @param \Stash\Interfaces\PoolInterface $cache
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct(ObjectServiceInterface $objectService, ClientInterface $client = NULL, PoolInterface $cache = NULL, LoggerInterface $logger = NULL) {
    $uri = $objectService->generateUri('/notify', TRUE);
    // Ensure we only filter to objects of this type.
    $uri->getQuery()->set('filter', $objectService->getObjectType());
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
  public static function create(ObjectServiceInterface $objectService, Container $container) {
    return new static(
      $objectService,
      $container['client'],
      $container['cache'],
      $container['logger']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processNotifications(array $notifications) {
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
  public function processNotificationReset($id) {
    $this->objectService->resetCache();

    parent::processNotificationReset($id);
  }

}
