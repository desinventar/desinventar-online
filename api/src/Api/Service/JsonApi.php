<?php

namespace Api\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonApi
{
    public function data($values)
    {
        $data = [
            'data' => $values,
        ];
        $status = 200;
        $headers = [];
        return new JsonResponse($data, $status, $headers);
    }

    public function error($values, $status = 200, $headers = [])
    {
        $data = ['errors' => [$values]];
        return new JsonResponse($data, $status, $headers);
    }
}
