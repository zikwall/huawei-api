<?php

namespace zikwall\huawei_api\services;

use zikwall\huawei_api\constants\HuaweiConstants;

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

        $regionUri = HuaweiConstants::URIS[$region];

        return sprintf("%s/%s", $this->injectService($this->name, $regionUri), $this->url);
    }

    private function injectService(string $serviceName, string $url) : string
    {
        return str_replace('{{service}}', $serviceName, $url);
    }
}
