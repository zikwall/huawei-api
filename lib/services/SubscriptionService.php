<?php

namespace zikwall\huawei_api\services;

use GuzzleHttp\Client;
use zikwall\huawei_api\HuaweiClient;

class SubscriptionService extends BaseService
{
    // https://developer.huawei.com/consumer/en/doc/development/HMS-References/iap-api-specification-related-v4#h1-1578554539083-0
    const TOBTOC_SITE_URL = 'https://subscr-drru.iap.hicloud.com/sub/applications/v2/purchases/get';

    public static function buildServiceUri(): string
    {
        return static::TOBTOC_SITE_URL;
    }

    /**
     * @param string $accessToken
     * @param string $purchaseToken
     * @param string $subscriptionId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getSubscription(string $accessToken, string $purchaseToken, string $subscriptionId) : array
    {
        $client = new Client();
        $response = $client->request('POST', static::buildServiceUri(),
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
                        HuaweiClient::makeAuthorizationHeaders($accessToken)
                    )
            ]);

        if ($response->getStatusCode() !== 200) {
            throw new \BadMethodCallException("invalid request to access token");
        }

        $response = json_decode($response->getBody()->getContents(), true);
        return $response;
    }
}