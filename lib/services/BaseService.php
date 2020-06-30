<?php

namespace zikwall\huawei_api\services;

use zikwall\huawei_api\utils\Region;

abstract class BaseService implements ServiceInterface
{
    const SERVICES = [
        'subscr', 'orders'
    ];

    private $_apiVersion = 'v2';

    public function getApiVersion(): string
    {
        return $this->_apiVersion;
    }

    public function setApiVersion(string $version): void
    {
        $this->_apiVersion = $version;
    }

    protected static function buildServiceUri(string $region) : string
    {
        $service = static::getServiceName();

        if (!in_array($service, static::SERVICES)) {
            throw new \InvalidArgumentException('invalid service name');
        }

        $region = Region::URIS[$region];
        $api    = static::getUrlPart();
        $url    = static::injectService($service, $region);

        return sprintf("%s/%s", $url, $api);
    }

    protected static function injectService(string $serviceName, string $url) : string
    {
        return str_replace('{{service}}', $serviceName, $url);
    }
}