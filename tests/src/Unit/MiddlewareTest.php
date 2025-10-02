<?php

namespace Lullabot\Mpx\Tests\Unit;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Lullabot\Mpx\Exception\MpxExceptionInterface;
use Lullabot\Mpx\Middleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @coversDefaultClass \Lullabot\Mpx\Middleware
 */
class MiddlewareTest extends TestCase
{
    /**
     * Test that a response with no JSON doesn't throw an error.
     *
     * @covers ::mpxErrors
     */
    public function testMpxEmptyContentType()
    {
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $response = new Response(200);
        $processedResponse = $this->getResponse($request, $response, Middleware::mpxErrors());

        $this->assertEquals($response, $processedResponse);
    }

    /**
     * Test that a response with no JSON doesn't throw an error.
     *
     * @covers ::mpxErrors
     */
    public function testNoResponseData()
    {
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $response = new Response(200, ['Content-Type' => 'application/json'], '{}');
        $processedResponse = $this->getResponse($request, $response, Middleware::mpxErrors());
        $this->assertEquals($response, $processedResponse);

        // Test text/json as well.
        $response = new Response(200, ['Content-Type' => 'text/json'], '{}');
        $processedResponse = $this->getResponse($request, $response, Middleware::mpxErrors());
        $this->assertEquals($response, $processedResponse);
    }

    /**
     * Test that a response with no JSON doesn't throw an error.
     *
     * @covers ::mpxErrors
     */
    public function testExceptionThrown()
    {
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $body = json_encode([
            'isException' => true,
            'responseCode' => 401,
            'title' => 'Unauthorized',
            'description' => 'Token expired',
        ]);
        $response = new Response(200, ['Content-Type' => 'application/json'], $body);
        $this->expectException(MpxExceptionInterface::class);
        $this->expectExceptionMessage('Error Unauthorized: Token expired');
        $this->getResponse($request, $response, Middleware::mpxErrors());
    }

    /**
     * Tests throwing mpx notification errors.
     *
     * @covers ::mpxErrors
     */
    public function testNotificationException()
    {
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $body = json_encode([
            [
                'type' => 'Exception',
                'entry' => [
                    'isException' => true,
                    'responseCode' => 404,
                    'title' => 'ObjectNotFoundException',
                    'description' => 'Sequence 1234 is no longer available',
                ],
            ],
        ]);
        $response = new Response(200, ['Content-Type' => 'application/json'], $body);
        $this->expectException(MpxExceptionInterface::class);
        $this->expectExceptionMessage('Sequence 1234 is no longer available');
        $this->getResponse($request, $response, Middleware::mpxErrors());
    }

    /**
     * Get the response from the MPX error handler.
     *
     * @return ResponseInterface
     */
    private function getResponse(RequestInterface $request, ResponseInterface $response, callable $errorHandler)
    {
        $handler = fn (RequestInterface $request, array $options) => new FulfilledPromise($response);
        $fn = $errorHandler($handler);

        /** @var \GuzzleHttp\Promise\FulfilledPromise $promise */
        $promise = $fn($request, []);
        $processedResponse = $promise->wait();

        return $processedResponse;
    }
}
