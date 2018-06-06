<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\QQuery;

use Lullabot\Mpx\DataService\QQuery\TermGroup;
use Lullabot\Mpx\DataService\QQuery\Term;
use PHPUnit\Framework\TestCase;

class TermGroupTest extends TestCase
{
    public function testToString()
    {
        $term = new Term('value');
        $group = new TermGroup($term);
        $this->assertEquals('"value"', (string) $group);
    }

    public function testAndTerm()
    {
        $term = new Term('value');
        $group = new TermGroup($term);
        $group->and(new Term('value2'));
        $this->assertEquals('"value" AND "value2"', (string) $group);
    }

    public function testOrTerm()
    {
        $term = new Term('value');
        $group = new TermGroup($term);
        $group->or(new Term('value2'));
        $this->assertEquals('"value" OR "value2"', (string) $group);
    }

    public function testWrap()
    {
        $term = new Term('value');
        $group = new TermGroup($term);
        $group->or(new Term('value2'))
            ->wrapParenthesis();
        $this->assertEquals('("value" OR "value2")', (string) $group);
    }

    public function testSubWrap()
    {
        $andGroup = new TermGroup(new Term('value2'));
        $andGroup->and(new Term('value3'))
            ->wrapParenthesis();

        $orGroup = new TermGroup(new Term('value1'));
        $orGroup->or($andGroup);
        $this->assertEquals('"value1" OR ("value2" AND "value3")', (string) $orGroup);
    }
}
