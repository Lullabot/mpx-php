<?php

namespace Lullabot\Mpx\Tests\Fixtures;

use Symfony\Component\Lock\BlockingStoreInterface;
use Symfony\Component\Lock\PersistingStoreInterface;

/**
 * Dummy interface to use with getMockBuilder().
 */
interface DummyStoreInterface extends PersistingStoreInterface, BlockingStoreInterface
{
}
