<?php

namespace ApiTest\General;

use \ApiTest\ApiTestCase;

use GuzzleHttp\Exception\ClientException;

final class ErrorResponseTest extends ApiTestCase
{
    public function testVersion()
    {
        $response = null;
        try {
            $response = $this->http->get('non_existent_endpoint');
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
            }
        }
        $this->assertEquals(404, $response->getStatusCode());
        $body = $this->jsonResponse($response);
        $this->assertTrue(isset($body['errors']));
        $this->assertTrue(count($body['errors']) > 0);
        foreach ($body['errors'] as $error) {
            $this->assertTrue(isset($error['code']));
            $this->assertTrue(isset($error['message']));
        }
    }
}
