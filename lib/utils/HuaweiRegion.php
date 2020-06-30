<?php

namespace zikwall\huawei_api\utils;

class HuaweiRegion
{
    const CHINA = 'China';
    const GERMANY = 'Germany';
    const SINGAPORE = 'Singapore';
    const RUSSIA = 'Russia';

    // https://developer.huawei.com/consumer/en/doc/development/HMS-References/iap-api-specification-related-v4#h1-1578554539083-0
    const URIS = [
        HuaweiRegion::CHINA     => 'https://{{service}}-drcn.iap.hicloud.com',
        HuaweiRegion::GERMANY   => 'https://{{service}}-dre.iap.hicloud.com',
        HuaweiRegion::SINGAPORE => 'https://{{service}}-dra.iap.hicloud.com',
        HuaweiRegion::RUSSIA    => 'https://{{service}}-drru.iap.hicloud.com',
    ];

    public static function getList() : array
    {
        return [
            static::CHINA,
            static::GERMANY,
            static::SINGAPORE,
            static::RUSSIA
        ];
    }

    public static function isAvailable(string $region) : bool
    {
        return in_array($region, static::getList());
    }
}
