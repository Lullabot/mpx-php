<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\QQuery;

use Lullabot\Mpx\DataService\QQuery\Term;
use PHPUnit\Framework\TestCase;

/**
 * Tests creating terms for Q queries.
 *
 * @covers \Lullabot\Mpx\DataService\QQuery\Term
 */
class TermTest extends TestCase
{
    public function testGetNamespace()
    {
        $term = new Term('value');
        $this->assertEquals('namespace', $term->setNamespace('namespace')
            ->getNamespace());
    }

    public function testIsExclude()
    {
        $term = new Term('value');
        $this->assertTrue($term->exclude()->isExclude());
        $this->assertFalse($term->require()->isExclude());
    }

    public function testGetValue()
    {
        $term = new Term('value');
        $this->assertEquals('value', $term->getValue());
        $this->assertEquals('value2', $term->setValue('value2')->getValue());
    }

    public function testGetField()
    {
        $term = new Term('value', 'field');
        $this->assertEquals('field', $term->getField());
        $this->assertEquals('field2', $term->setField('field2')->getField());
    }

    public function testOptional()
    {
        $term = new Term('value');
        $this->assertFalse($term->optional()->isExclude());
        $this->assertFalse($term->optional()->isRequired());
    }

    public function testGetBoost()
    {
        $term = new Term('value');
        $this->assertEquals(5, $term->setBoost(5)->getBoost());
    }

    public function testGetMatchType()
    {
        $term = new Term('value', 'field');
        $this->assertEquals('exact', $term->setMatchType('exact')->getMatchType());
    }

    public function testToQueryParts()
    {
        $term = new Term('value', 'field');
        $this->assertEquals([
            'q' => 'field:"value"',
        ], $term->toQueryParts());
    }

    public function testSetValue()
    {
        $term = new Term('value');
        $this->assertEquals('value2', $term->setValue('value2')->getValue());
    }

    public function testIsRequired()
    {
        $term = new Term('value');
        $this->assertFalse($term->require()->isExclude());
        $this->assertTrue($term->isRequired());
    }

    public function testToString()
    {
        $term = new Term('value');
        $this->assertEquals('"value"', (string) $term);
        $term = new Term('value', 'field');
        $this->assertEquals('field:"value"', (string) $term);
        $term = new Term('value', 'field', 'namespace');
        $this->assertEquals('namespace$field:"value"', (string) $term);

        $term->setMatchType('exact');
        $this->assertEquals('namespace$field.exact:"value"', (string) $term);

        $term->require();
        $this->assertEquals('+namespace$field.exact:"value"', (string) $term);

        $term->exclude();
        $this->assertEquals('-namespace$field.exact:"value"', (string) $term);

        $term->optional();
        $this->assertEquals('namespace$field.exact:"value"', (string) $term);

        $term->setBoost(5);
        $this->assertEquals('namespace$field.exact:"value"^5', (string) $term);
    }

    /**
     * @param $value
     * @param $escaped
     * @dataProvider escapeDataProvider
     */
    public function testEscape($value, $escaped)
    {
        $term = new Term($value);
        $this->assertEquals($escaped, (string) $term);
    }

    public function escapeDataProvider()
    {
        $cases = [];
        foreach (Term::ESCAPE_CHARACTERS as $escape => $replace) {
            $cases['escaping '.$escape] = ['value'.$escape, '"value'.$replace.'"'];
        }

        $cases['escaping multiple characters'] = ['value\+', '"value\\\\\\+"'];

        return $cases;
    }

    public function testWrap()
    {
        $term = new Term('value');
        $term->wrapParenthesis();
        $this->assertEquals('("value")', (string) $term);
    }

    public function testSetNamespace()
    {
        $term = new Term('value', 'field', 'namespace');
        $this->assertEquals('namespace', $term->getNamespace());
        $this->assertEquals('namespace2', $term->setNamespace('namespace2')->getNamespace());
    }
}
