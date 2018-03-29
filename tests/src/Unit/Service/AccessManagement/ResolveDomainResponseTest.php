<?php

namespace Lullabot\Mpx\Tests\Unit\Service\AccessManagement;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\Service\AccessManagement\ResolveDomainResponse;
use PHPUnit\Framework\TestCase;

/**
 * Tests ResolveDomainResponses.
 *
 * @coversDefaultClass \Lullabot\Mpx\Service\AccessManagement\ResolveDomainResponse
 */
class ResolveDomainResponseTest extends TestCase
{
    /**
     * Test retrieving all found services.
     *
     * @covers ::setResolveDomainResponse
     * @covers ::getServices
     */
    public function testGetServices()
    {
        $response = new ResolveDomainResponse();
        $services = [
            'first service' => new Uri('http://example.com/1'),
            'second service' => new Uri('http://example.com/2'),
        ];
        $response->setResolveDomainResponse($services);

        $this->assertEquals(array_keys($services), $response->getServices());
    }

    /**
     * Test getting service URLs.
     *
     * @covers ::getUrl
     */
    public function testGetUrl()
    {
        $response = new ResolveDomainResponse();
        $first = new Uri('http://example.com/1');
        $second = new Uri('http://example.com/2');
        $services = [
            'first service' => $first,
            'second service' => $second,
        ];
        $response->setResolveDomainResponse($services);

        $this->assertEquals($first->withScheme('https'), $response->getUrl('first service'));
        $this->assertSame($second, $response->getUrl('second service', true));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('not found service was not found.');
        $response->getUrl('not found');
    }
}
