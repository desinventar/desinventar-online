<?php

namespace ApiTest;

use \GuzzleHttp\Client;

class ApiTestCase extends \PHPUnit_Framework_TestCase
{
    protected $http;

    public function setUp()
    {
        $apiUrl = $this->url = $this->getVar('DESINVENTAR_API_URL', '');
        $this->http = new Client(['base_uri' => $apiUrl]);
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
