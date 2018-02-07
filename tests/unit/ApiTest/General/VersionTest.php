<?php

namespace ApiTest\General;

use \ApiTest\ApiTestCase;

final class VersionTest extends ApiTestCase
{
    public function testVersion()
    {
        $response = $this->http->get('common/version');
        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()['Content-Type'][0];
        $this->assertEquals('application/json;charset=utf-8', $contentType);

        $body = $this->jsonResponse($response);
        foreach (['major_version', 'version', 'release_date'] as $field) {
            $this->assertTrue(isset($body['data'][$field]));
        }
        $this->assertTrue(substr($body['data']['major_version'], 0, 2) === '10');
        $this->assertTrue(substr($body['data']['version'], 0, 2) === '10');
    }
}
