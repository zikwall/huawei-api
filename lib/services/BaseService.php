<?php

namespace zikwall\huawei_api\services;

use zikwall\huawei_api\utils\Region;

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

    public static function buildServiceUri(string $service, string $region, string $part) : string
    {
        $url = static::injectService($service, Region::URIS[$region]);
        return sprintf("%s/%s", $url, $part);
    }

    public static function injectService(string $serviceName, string $url) : string
    {
        return str_replace('{{service}}', $serviceName, $url);
    }
}