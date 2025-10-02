<?php

namespace Lullabot\Mpx\Tests\Unit\Service\AccessManagement;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\Service\AccessManagement\ResolveDomainResponse;
use Lullabot\Mpx\Service\AccessManagement\ResolveDomainResponseExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Type;

/**
 * Tests mapping resolve domain array responses to strings and URIs.
 *
 * @coversDefaultClass \Lullabot\Mpx\Service\AccessManagement\ResolveDomainResponseExtractor
 */
class ResolveDomainResponseExtractorTest extends TestCase
{
    /**
     * @covers ::getTypes
     */
    public function testGetTypes()
    {
        $extractor = new ResolveDomainResponseExtractor();
        $types = $extractor->getTypes(ResolveDomainResponse::class, 'resolveDomainResponse');
        $this->assertCount(1, $types);
        /** @var Type $type */
        $type = $types[0];
        $this->assertEquals(Type::BUILTIN_TYPE_ARRAY, $type->getBuiltinType());
        $this->assertEquals(Type::BUILTIN_TYPE_STRING, $type->getCollectionKeyTypes()[0]->getBuiltinType());
        $this->assertEquals('object', $type->getCollectionValueTypes()[0]->getBuiltinType());
        $this->assertEquals(Uri::class, $type->getCollectionValueTypes()[0]->getClassName());
    }

    /**
     * @covers ::getTypes
     */
    public function testInvalidProperty()
    {
        $extractor = new ResolveDomainResponseExtractor();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('This extractor only supports resolveDomainResponse properties.');
        $extractor->getTypes('', 'not-valid');
    }
}
