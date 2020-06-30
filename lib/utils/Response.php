<?php

namespace zikwall\huawei_api\utils;

use Psr\Http\Message\ResponseInterface;

class Response
{
    private $_response = null;

    public function __construct(ResponseInterface $response)
    {
        $this->_response = $response;
    }

    public function toMap() : array
    {
        return json_decode($this->_response->getBody()->getContents(), true);
    }

    public function isOk() : bool
    {
        return $this->_response->getStatusCode() === 200;
    }
}