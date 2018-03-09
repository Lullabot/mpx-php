<?php

namespace Lullabot\Mpx\Tests\Unit\Exception;

use GuzzleHttp\Psr7\Response;
use Lullabot\Mpx\Exception\ClientException;
use Lullabot\Mpx\Exception\MpxExceptionFactory;
use Lullabot\Mpx\Exception\ServerException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * @coversDefaultClass \Lullabot\Mpx\Exception\MpxExceptionFactory
 */
class MpxExceptionFactoryTest extends TestCase {

    /**
     * @covers ::create
     */
    public function testCreateClientException() {
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $data = [
            'responseCode' => 403,
            'isException' => true,
            'title' => 'Access denied',
            'description' => 'Authentication credentials invalid',
        ];
        $response = new Response(200, [], json_encode($data));

        $exception = MpxExceptionFactory::create($request, $response);
        $this->assertInstanceOf(ClientException::class, $exception);
        $this->assertEquals($data['responseCode'], $exception->getResponse()->getStatusCode());
        $this->assertEquals($data['title'], $exception->getResponse()->getReasonPhrase());
    }

    /**
     * @covers ::create
     */
    public function testCreateServerException() {
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $data = [
            'responseCode' => 503,
            'isException' => true,
            'title' => 'Internal error',
            'description' => 'It is totally the worst',
        ];
        $response = new Response(200, [], json_encode($data));

        $exception = MpxExceptionFactory::create($request, $response);
        $this->assertInstanceOf(ServerException::class, $exception);
        $this->assertEquals($data['responseCode'], $exception->getResponse()->getStatusCode());
        $this->assertEquals($data['title'], $exception->getResponse()->getReasonPhrase());
    }
}
