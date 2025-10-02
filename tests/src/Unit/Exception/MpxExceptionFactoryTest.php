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
class MpxExceptionFactoryTest extends TestCase
{
    /**
     * @covers ::create
     * @covers ::createException
     */
    public function testCreateClientException()
    {
        /** @var RequestInterface $request */
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
     * @covers ::createException
     */
    public function testCreateServerException()
    {
        /** @var RequestInterface $request */
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

    /**
     * Test creating an exception from a notification error.
     *
     * @covers ::createFromNotificationException
     * @covers ::createException
     */
    public function testCreateFromNotificationException()
    {
        /** @var RequestInterface $request */
        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $data = [
            [
                'type' => 'Exception',
                'entry' => [
                    'responseCode' => 503,
                    'isException' => true,
                    'title' => 'Internal error',
                    'description' => 'It is totally the worst',
                ],
            ],
        ];
        $response = new Response(200, [], json_encode($data));

        $exception = MpxExceptionFactory::createFromNotificationException($request, $response);
        $this->assertInstanceOf(ServerException::class, $exception);
        $this->assertEquals($data[0]['entry']['responseCode'], $exception->getResponse()->getStatusCode());
        $this->assertEquals($data[0]['entry']['title'], $exception->getResponse()->getReasonPhrase());
    }
}
