<?php

namespace zikwall\huawei_api\services;

use zikwall\huawei_api\HuaweiClient;
use zikwall\huawei_api\utils\Response;

class OrderService extends BaseService
{
    const URL_PART = 'applications/purchases/tokens/verify';

    /**
     * @param HuaweiClient $client
     * @param string $purchaseToken
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function verifyToken(HuaweiClient $client, string $purchaseToken) : array
    {
        $response = $client->getHttpClient()->request('POST',
            static::buildServiceUri('orders', $client->getRegion(), static::URL_PART),
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