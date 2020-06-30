<?php

namespace zikwall\huawei_api\services;

use zikwall\huawei_api\utils\HuaweiRegion;

class HuaweiServiceBase implements HuaweiServiceInterface
{
    /**
     * @var string
     */
    protected $url = '';
    /**
     * @var string
     */
    protected $name = '';

    const SERVICES = [
        'subscr', 'orders'
    ];

    protected function buildServiceUri(string $region) : string
    {
        if (!in_array($this->name, static::SERVICES)) {
            throw new \InvalidArgumentException('invalid service name');
        }

        $region = HuaweiRegion::URIS[$region];

        return sprintf("%s/%s", $this->injectService($this->name, $region), $this->url);
    }

    private function injectService(string $serviceName, string $url) : string
    {
        return str_replace('{{service}}', $serviceName, $url);
    }
}
