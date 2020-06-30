<?php

namespace zikwall\huawei_api\http;

use GuzzleHttp\Client;

trait HttpClient
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $httpClient = null;

    protected function makeHttpClient() : void
    {
        $this->httpClient = new Client();
    }

    protected function getHttpClient() : Client
    {
        return $this->httpClient;
    }
}
