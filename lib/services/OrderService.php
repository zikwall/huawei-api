<?php

namespace zikwall\huawei_api\services;

use zikwall\huawei_api\HuaweiClient;
use zikwall\huawei_api\utils\Region;
use zikwall\huawei_api\utils\Response;

class OrderService extends BaseService
{
    // https://developer.huawei.com/consumer/en/doc/development/HMS-References/iap-api-specification-related-v4#h1-1578554539083-0
    const URIS = [
        Region::CHINA     => 'https://orders-drcn.iap.hicloud.com',
        Region::GERMANY   => 'https://orders-dre.iap.hicloud.com',
        Region::SINGAPORE => 'https://orders-dra.iap.hicloud.com',
        Region::RUSSIA    => 'https://orders-drru.iap.hicloud.com',
    ];

    const URL_PART = 'applications/purchases/tokens/verify';

    public static function buildServiceUri(string $region) : string
    {
        return sprintf("%s/%s", static::URIS[$region], static::URL_PART);
    }

    /**
     * @param HuaweiClient $client
     * @param string $purchaseToken
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function verifyToken(HuaweiClient $client, string $purchaseToken) : array
    {
        $response = $client->getHttpClient()->request('POST', static::buildServiceUri($client->getRegion()),
            [
                'body' => json_encode([
                    'purchaseToken' => $purchaseToken,
                    'productId'     => $client->getProductId(),
                ]),
                'headers' =>
                    array_merge(
                        [
                            'Content-Type' => 'application/json; charset=UTF-8'
                        ],
                        HuaweiClient::makeAuthorizationHeaders($client->getAccessToken())
                    )
            ]);

        $response = new Response($response);

        if ($response->isOk() === false) {
            throw new \BadMethodCallException("invalid request to orders");
        }

        return $response->toMap();
    }
}