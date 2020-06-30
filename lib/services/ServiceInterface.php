<?php

namespace zikwall\huawei_api\services;

interface ServiceInterface
{
    public function setApiVersion(string $version) : void;
    public function getApiVersion() : string;

    public static function getUrlPart() : string;
    public static function getServiceName() : string;
}