<?php

namespace zikwall\huawei_api\constants;

use zikwall\huawei_api\utils\HuaweiRegion;

class HuaweiConstants
{
    const DEFAULT_CONFIG_FILE_NAME = 'agconnect-services';
    const DEFAULT_REGION = HuaweiRegion::RUSSIA;

    // https://developer.huawei.com/consumer/en/doc/development/HMS-References/iap-api-specification-related-v4#h1-1578554539083-0
    const URIS = [
        HuaweiRegion::CHINA     => 'https://{{service}}-drcn.iap.hicloud.com',
        HuaweiRegion::GERMANY   => 'https://{{service}}-dre.iap.hicloud.com',
        HuaweiRegion::SINGAPORE => 'https://{{service}}-dra.iap.hicloud.com',
        HuaweiRegion::RUSSIA    => 'https://{{service}}-drru.iap.hicloud.com',
    ];
}
