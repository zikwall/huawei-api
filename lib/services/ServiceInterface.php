<?php

namespace zikwall\huawei_api\services;

interface ServiceInterface
{
    public function setApiVersion(string $version) : void;
    public function getApiVersion() : string;
}