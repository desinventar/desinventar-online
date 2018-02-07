<?php

namespace ApiTest\General;

use \ApiTest\ApiTestCase;

final class RootTest extends ApiTestCase
{
    public function testApiRoot()
    {
        $response = $this->http->get($this->baseUrl);
        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals('application/json;charset=utf-8', $contentType);

        $body = $this->jsonResponse($response);
        $this->assertTrue(isset($body['data']['text']));
        $this->assertTrue($body['data']['text'] === 'DesInventar Api Server');
    }
}
