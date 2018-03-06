<?php

namespace ApiTest;

use \GuzzleHttp\Client;

class ApiTestCase extends \PHPUnit_Framework_TestCase
{
    protected $http;
    protected $baseUrl;

    public function setUp()
    {
        $this->baseUrl = $this->getVar('DESINVENTAR_TEST_API_URL', '');
        $this->http = new Client(['base_uri' => $this->baseUrl]);
    }

    public function tearDown()
    {
        $this->http = null;
    }

    protected function getVar($key, $defaultValue = '')
    {
        $value = getenv($key);
        if (!empty($value)) {
            return $value;
        }
        if (!empty($GLOBALS[$key])) {
            return $GLOBALS[$key];
        }
        return $defaultValue;
    }

    protected function jsonResponse($response)
    {
        $body = (string)$response->getBody();
        $body = json_decode($body, true);
        return $body;
    }
}
