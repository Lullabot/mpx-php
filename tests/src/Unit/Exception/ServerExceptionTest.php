<?php

namespace Lullabot\Mpx\Tests\Unit\Exception;

use GuzzleHttp\Psr7\Request;
use Lullabot\Mpx\Exception\ServerException;
use Lullabot\Mpx\Tests\JsonResponse;
use PHPUnit\Framework\TestCase;

class ServerExceptionTest extends TestCase
{
    /**
     * Test parsing a response into a server exception.
     *
     * @covers \Lullabot\Mpx\Exception\ServerException::__construct
     * @covers \Lullabot\Mpx\Exception\MpxExceptionTrait::parseResponse
     */
    public function testConstruct()
    {
        /** @var \GuzzleHttp\Psr7\Request $request */
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        // We don't have an example of a real 503, so we override this 401.
        $response = new JsonResponse(503, [], 'invalid-token.json');
        $e = new ServerException($request, $response);
        $this->assertEquals('HTTP 503 Error com.theplatform.authentication.api.exception.InvalidTokenException: Invalid token', $e->getMessage());
    }
}
