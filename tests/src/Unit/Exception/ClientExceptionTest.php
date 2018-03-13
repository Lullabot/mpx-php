<?php

namespace Lullabot\Mpx\Tests\Unit\Exception;

use GuzzleHttp\Psr7\Request;
use Lullabot\Mpx\Exception\ClientException;
use Lullabot\Mpx\Tests\JsonResponse;
use PHPUnit\Framework\TestCase;

class ClientExceptionTest extends TestCase {

    /**
     * Test parsing a response into a client exception.
     *
     * @covers \Lullabot\Mpx\Exception\ClientException::__construct
     * @covers \Lullabot\Mpx\Exception\MpxExceptionTrait::parseResponse
     */
    public function testConstruct() {
        /** @var \GuzzleHttp\Psr7\Request $request */
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $response = new JsonResponse(401, [], 'invalid-token.json');
        $e = new ClientException($request, $response);
        $this->assertEquals('HTTP 401 Error com.theplatform.authentication.api.exception.InvalidTokenException: Invalid token', $e->getMessage());
    }
}
