<?php

namespace Lullabot\Mpx\Tests\Unit\Service\AccessManagement;

use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\Service\AccessManagement\ResolveAllUrlsResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lullabot\Mpx\Service\AccessManagement\ResolveAllUrlsResponse
 */
class ResolveAllUrlsResponseTest extends TestCase
{
    public function testGetService()
    {
        $response = new ResolveAllUrlsResponse();
        $response->setService('Kitten Data Service');
        $this->assertEquals('Kitten Data Service', $response->getService());
    }

    public function testResolve()
    {
        $response = new ResolveAllUrlsResponse();
        $response->setResolveAllUrlsResponse([
            new Uri('http://www.example.com'),
        ]);

        $this->assertEquals('https://www.example.com', $response->getUrl());
        $this->assertEquals('http://www.example.com', $response->getUrl(true));
    }
}
