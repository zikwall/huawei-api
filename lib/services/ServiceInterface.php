<?php

namespace zikwall\huawei_api\services;

interface ServiceInterface
{
    public function setApiVersion(string $version) : void;
    public function getApiVersion() : string;

    public static function buildServiceUri(string $service, string $region, string $part) : string;
    public static function injectService(string $serviceName, string $url) : string ;
}