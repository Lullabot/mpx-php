<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Lullabot\Mpx\DataService\Sort;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass  \Lullabot\Mpx\DataService\Sort
 */
class SortTest extends TestCase
{
    /**
     * @covers ::addSort
     * @covers ::toQueryParts
     */
    public function testAddSort()
    {
        $sort = new Sort();
        $sort->addSort('id', true);
        $sort->addSort('title');
        $this->assertEquals(['sort' => 'id|desc,title'], $sort->toQueryParts());
    }

}
