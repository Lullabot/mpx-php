<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Lullabot\Mpx\DataService\NotificationTypeExtractor;
use PHPUnit\Framework\TestCase;

/**
 * Test extracting the type of notification object returned from MPX.
 *
 * @coversDefaultClass \Lullabot\Mpx\DataService\NotificationTypeExtractor
 */
class NotificationTypeExtractorTest extends TestCase
{
    /**
     * Test normal fetching of an entry type.
     *
     * @covers ::setClass
     * @covers ::getTypes
     */
    public function testGetTypes()
    {
        $extractor = NotificationTypeExtractor::create();
        $extractor->setClass(static::class);
        $type = $extractor->getTypes('', 'entry');
        $this->assertEquals(static::class, $type[0]->getClassName());
    }

    /**
     * Test trying to extract a non-entry property.
     *
     * @covers ::getTypes
     */
    public function testNotEntry()
    {
        $extractor = NotificationTypeExtractor::create();
        $extractor->setClass(static::class);
        $this->assertNull($extractor->getTypes('', 'not-entry'));
    }

    /**
     * Test trying to extract before setting a target class.
     *
     * @covers ::getTypes
     */
    public function testClassNotSet()
    {
        $extractor = NotificationTypeExtractor::create();
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('setClass() must be called before using this extractor.');
        $extractor->getTypes('', 'entry');
    }
}
