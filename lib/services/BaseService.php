<?php

namespace zikwall\huawei_api\services;

class BaseService implements ServiceInterface
{
    private $_apiVersion = 'v2';

    public function getApiVersion(): string
    {
        return $this->_apiVersion;
    }

    public function setApiVersion(string $version): void
    {
        $this->_apiVersion = $version;
    }

    public static function buildServiceUri(string $region): string
    {
        return 'not implemented';
    }
}