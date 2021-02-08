<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Access;

use Lullabot\Mpx\DataService\Access\Account;
use Lullabot\Mpx\DataService\DataServiceExtractor;
use Lullabot\Mpx\DataService\DateTime\ConcreteDateTime;
use Lullabot\Mpx\Tests\Unit\DataService\ObjectTestBase;

/**
 * Test the Account data object.
 *
 * @covers \Lullabot\Mpx\DataService\Access\Account
 */
class AccountTest extends ObjectTestBase
{
    protected $class = Account::class;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $dataServiceExtractor = new DataServiceExtractor();
        $dataServiceExtractor->setClass($this->class);
        $this->loadFixture('account-object.json', $dataServiceExtractor);
    }

    /**
     * Test get / set methods.
     *
     * @param string $field    The field on Player to test.
     * @param mixed  $expected (optional) Override the assertion if data is converted, such as with timestamps.
     *
     * @dataProvider getSetMethods
     */
    public function testGetSet(string $field, $expected = null)
    {
        $this->assertObjectClass($this->class, $field, $expected);
    }

    /**
     * @param string $class
     *
     * @return array
     */
    public function getSetMethods()
    {
        $tests = parent::getSetMethods();
        $tests['added'] = ['added', new ConcreteDateTime(\DateTime::createFromFormat('U.u', '1310073366.000'))];
        $tests['updated'] = ['updated', new ConcreteDateTime(\DateTime::createFromFormat('U.u', '1319652154.000'))];

        return $tests;
    }
}
