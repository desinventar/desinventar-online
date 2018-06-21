<?php

namespace DesInventar\Service;

use \Psr\Http\Message\ResponseInterface as Response;

class JsonApi
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function data($values)
    {
        $data = [
            'data' => $values,
        ];
        return $this->jsonResponse($data, 200);
    }

    public function error($values, $status = 200)
    {
        $data = [
            'errors' => [$values]
        ];
        return $this->jsonResponse($data, $status);
    }

    private function jsonResponse($data, $status)
    {
        return $this->response
            ->withStatus($status)
            ->withJson($data);
    }
}
