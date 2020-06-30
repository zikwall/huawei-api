<?php

namespace zikwall\huawei_api\http;

use GuzzleHttp\Client;

trait HttpClient
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $httpClient = null;

    private function makeHttpClient() : void
    {
        $this->httpClient = new Client();
    }

    public function getHttpClient() : Client
    {
        return $this->httpClient;
    }
}