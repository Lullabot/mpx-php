<?php

declare(strict_types=1);

namespace Lullabot\Mpx\Tests\Unit\DataService {
// Added imports on purpose as mock for the unit tests, please do not remove.
    use Lullabot\Mpx\DataService\CachingContextFactory;
    use Mockery as m;
    use phpDocumentor\Reflection\DocBlock;
    use phpDocumentor\Reflection\DocBlock\Tag;
    use PHPUnit\Framework\TestCase;
    use ReflectionClass; // yes, the slash is part of the test

    /**
     * @coversDefaultClass \Lullabot\Mpx\DataService\CachingContextFactory
     * @covers ::<private>
     */
    class CachingContextFactoryTest extends TestCase
    {
        /**
         * @covers ::createFromReflector
         * @covers ::createForNamespace
         *
         * @uses \phpDocumentor\Reflection\Types\Context
         */
        public function testReadsNamespaceFromClassReflection()
        {
            $fixture = new CachingContextFactory();
            $context = $fixture->createFromReflector(new ReflectionClass($this));

            $this->assertSame(__NAMESPACE__, $context->getNamespace());
        }

        /**
         * @covers ::createFromReflector
         * @covers ::createForNamespace
         *
         * @uses \phpDocumentor\Reflection\Types\Context
         */
        public function testReadsAliasesFromClassReflection()
        {
            $fixture = new CachingContextFactory();
            $expected = [
                'm' => m::class,
                'DocBlock' => DocBlock::class,
                'Tag' => Tag::class,
                'phpDocumentor' => 'phpDocumentor',
                'TestCase' => TestCase::class,
                ReflectionClass::class => ReflectionClass::class,
            ];
            $context = $fixture->createFromReflector(new ReflectionClass($this));

            $actual = $context->getNamespaceAliases();

            // sort so that order differences don't break it
            $this->assertSame(sort($expected), sort($actual));
        }

        /**
         * @covers ::createForNamespace
         *
         * @uses \phpDocumentor\Reflection\Types\Context
         */
        public function testReadsNamespaceFromProvidedNamespaceAndContent()
        {
            $fixture = new CachingContextFactory();
            $context = $fixture->createForNamespace(__NAMESPACE__, file_get_contents(__FILE__));

            $this->assertSame(__NAMESPACE__, $context->getNamespace());
        }

        /**
         * @covers ::createForNamespace
         *
         * @uses \phpDocumentor\Reflection\Types\Context
         */
        public function testReadsAliasesFromProvidedNamespaceAndContent()
        {
            $fixture = new CachingContextFactory();
            $expected = [
                'm' => m::class,
                'DocBlock' => DocBlock::class,
                'Tag' => Tag::class,
                'phpDocumentor' => 'phpDocumentor',
                'TestCase' => TestCase::class,
                ReflectionClass::class => ReflectionClass::class,
            ];
            $context = $fixture->createForNamespace(__NAMESPACE__, file_get_contents(__FILE__));

            $actual = $context->getNamespaceAliases();

            // sort so that order differences don't break it
            $this->assertSame(sort($expected), sort($actual));
        }

        /**
         * @covers ::createForNamespace
         *
         * @uses \phpDocumentor\Reflection\Types\Context
         */
        public function testTraitUseIsNotDetectedAsNamespaceUse()
        {
            $php = '<?php declare(strict_types=1);
                namespace Foo;

                trait FooTrait {}

                class FooClass {
                    use FooTrait;
                }
            ';

            $fixture = new CachingContextFactory();
            $context = $fixture->createForNamespace('Foo', $php);

            $this->assertSame([], $context->getNamespaceAliases());
        }

        /**
         * @covers ::createForNamespace
         *
         * @uses \phpDocumentor\Reflection\Types\Context
         */
        public function testAllOpeningBracesAreCheckedWhenSearchingForEndOfClass()
        {
            $php = '<?php declare(strict_types=1);
                namespace Foo;

                trait FooTrait {}
                trait BarTrait {}

                class FooClass {
                    use FooTrait;

                    public function bar()
                    {
                        echo "{$baz}";
                        echo "${baz}";
                    }
                }

                class BarClass {
                    use BarTrait;

                    public function bar()
                    {
                        echo "{$baz}";
                        echo "${baz}";
                    }
                }
            ';

            $fixture = new CachingContextFactory();
            $context = $fixture->createForNamespace('Foo', $php);

            $this->assertSame([], $context->getNamespaceAliases());
        }

        /**
         * @covers ::createFromReflector
         */
        public function testEmptyFileName()
        {
            $fixture = new CachingContextFactory();
            $context = $fixture->createFromReflector(new \ReflectionClass(\stdClass::class));

            $this->assertSame([], $context->getNamespaceAliases());
        }

        /**
         * @covers ::createFromReflector
         */
        public function testEvalDClass()
        {
            eval(<<<PHP
namespace Foo;

class Bar
{
}
PHP
            );
            $fixture = new CachingContextFactory();
            $context = $fixture->createFromReflector(new \ReflectionClass('Foo\Bar'));

            $this->assertSame([], $context->getNamespaceAliases());
        }

        protected function tearDown()
        {
            \Mockery::close();
        }
    }
}

namespace phpDocumentor\Reflection\Types\Mock {
    // the following import should not show in the tests above
    use phpDocumentor\Reflection\DocBlock\Description;

    class Foo extends Description
    {
        // dummy class
    }
}
