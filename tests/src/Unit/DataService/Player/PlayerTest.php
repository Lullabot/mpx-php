<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Player;

use Lullabot\Mpx\DataService\Player\Player;
use Lullabot\Mpx\Tests\Unit\DataService\ObjectTestBase;

/**
 * Tests the Player object.
 *
 * This class explicitly doesn't use method coverage markers because it would be hundreds of lines.
 *
 * @coversDefaultClass \Lullabot\Mpx\DataService\Player\Player
 */
class PlayerTest extends ObjectTestBase
{
    protected $class = Player::class;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->loadFixture('player-object.json');
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
        $tests['added'] = ['added', \DateTime::createFromFormat('U.u', '1335377369.000')];
        $tests['updated'] = ['updated', \DateTime::createFromFormat('U.u', '1335378125.000')];

        return $tests;
    }
}
