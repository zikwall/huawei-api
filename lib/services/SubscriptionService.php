<?php

namespace zikwall\huawei_api\services;

use zikwall\huawei_api\HuaweiClient;
use zikwall\huawei_api\utils\HuaweiResponseReader;

class SubscriptionService extends BaseService
{
    public static function getUrlPart() : string
    {
        return 'sub/applications/v2/purchases/get';
    }

    public static function getServiceName() : string
    {
        return 'subscr';
    }

    /**
     * @param HuaweiClient $client
     * @param string $purchaseToken
     * @param string $subscriptionId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getSubscription(HuaweiClient $client, string $purchaseToken, string $subscriptionId) : array
    {
        $response = $client->getHttpClient()->request('POST',
            static::buildServiceUri($client->getRegion()),
            [
                'body' => json_encode([
                    'subscriptionId' => $subscriptionId,
                    'purchaseToken'  => $purchaseToken,
                ]),
                'headers' =>
                    array_merge(
                        [
                            'Content-Type' => 'application/json; charset=UTF-8'
                        ],
                        HuaweiClient::makeAuthorizationHeaders($client->getAccessToken())
                    )
            ]);

        $response = new HuaweiResponseReader($response);

        if ($response->isOk() === false) {
            throw new \BadMethodCallException("invalid request to orders");
        }

        return $response->toMap();
    }
}
