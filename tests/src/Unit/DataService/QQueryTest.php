<?php

namespace Lullabot\Mpx\Tests\Unit\DataService;

use Lullabot\Mpx\DataService\QQuery;
use Lullabot\Mpx\DataService\Term;
use PHPUnit\Framework\TestCase;

class QQueryTest extends TestCase
{

    public function testToString()
    {
        $term = new Term('value');
        $query = new QQuery($term);
        $this->assertEquals('"value"', (string) $query);
    }

    public function testAndTerm()
    {
        $term = new Term('value');
        $query = new QQuery($term);
        $query->and(new Term('value2'));
        $this->assertEquals('"value" AND "value2"', (string) $query);
    }

    public function testOrTerm()
    {
        $term = new Term('value');
        $query = new QQuery($term);
        $query->or(new Term('value2'));
        $this->assertEquals('"value" OR "value2"', (string) $query);

    }
}
