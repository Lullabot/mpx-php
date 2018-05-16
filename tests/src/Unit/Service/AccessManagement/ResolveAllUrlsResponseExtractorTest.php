<?php

namespace Lullabot\Mpx\Tests\Unit\Service\AccessManagement;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\Service\AccessManagement\ResolveAllUrlsResponseExtractor;
use Lullabot\Mpx\Service\AccessManagement\ResolveAllUrlsResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Type;

/**
 * Tests mapping resolve domain array responses to strings and URIs.
 *
 * @coversDefaultClass \Lullabot\Mpx\Service\AccessManagement\ResolveAllUrlsResponseExtractor
 */
class ResolveAllUrlsResponseExtractorTest extends TestCase
{
    /**
     * @covers ::getTypes
     */
    public function testGetTypes()
    {
        $extractor = new ResolveAllUrlsResponseExtractor();
        $types = $extractor->getTypes(ResolveAllUrlsResponse::class, 'resolveAllUrlsResponse');
        $this->assertCount(1, $types);
        /** @var \Symfony\Component\PropertyInfo\Type $type */
        $type = $types[0];
        $this->assertEquals(Type::BUILTIN_TYPE_ARRAY, $type->getBuiltinType());
        $this->assertEquals(Type::BUILTIN_TYPE_STRING, $type->getCollectionKeyType()->getBuiltinType());
        $this->assertEquals('object', $type->getCollectionValueType()->getBuiltinType());
        $this->assertEquals(Uri::class, $type->getCollectionValueType()->getClassName());
    }

    /**
     * @covers ::getTypes
     */
    public function testInvalidProperty()
    {
        $extractor = new ResolveAllUrlsResponseExtractor();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('This extractor only supports resolveAllUrlsResponse properties.');
        $extractor->getTypes('', 'not-valid');
    }
}
