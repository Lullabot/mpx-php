<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\Player;

use Lullabot\Mpx\DataService\DateTime\ConcreteDateTime;
use Lullabot\Mpx\DataService\Player\Player;
use Lullabot\Mpx\Tests\Unit\DataService\ObjectTestBase;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;

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

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixture('player-object.json', new ReflectionExtractor());
    }

    /**
     * Test get / set methods.
     *
     * @param string $field    The field on Player to test.
     * @param mixed  $expected (optional) Override the assertion if data is converted, such as with timestamps.
     *
     * @dataProvider getSetMethods
     */
    public function testGetSet(string $field, mixed $expected = null)
    {
        $this->assertObjectClass($this->class, $field, $expected);
    }

    /**
     * @return array
     */
    public function getSetMethods()
    {
        $tests = parent::getSetMethods();
        $tests['added'] = ['added', new ConcreteDateTime(\DateTime::createFromFormat('U.u', '1335377369.000'))];
        $tests['updated'] = ['updated', new ConcreteDateTime(\DateTime::createFromFormat('U.u', '1335378125.000'))];

        return $tests;
    }
}
