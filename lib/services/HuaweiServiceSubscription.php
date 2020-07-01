<?php

namespace zikwall\huawei_api\services;

use zikwall\huawei_api\http\HttpClient;
use zikwall\huawei_api\HuaweiApiConfigurable;
use zikwall\huawei_api\HuaweiClient;
use zikwall\huawei_api\utils\HuaweiResponseReader;
use zikwall\huawei_api\utils\HuaweiRegion;

class HuaweiServiceSubscription extends HuaweiServiceBase
{
    use HttpClient;
    use HuaweiApiConfigurable;

    /**
     * @var string
     */
    protected $name = 'subscr';
    /**
     * @var string
     */
    protected $url = 'sub/applications/v2/purchases/get';

    public function __construct(string $accessToken, string $region = '')
    {
        $this->setAccessToken($accessToken);

        if ($region) {
            $this->setRegion($region);
        }

        $this->makeHttpClient();
    }

    /**
     * @param HuaweiClient $client
     * @param string $purchaseToken
     * @param string $subscriptionId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSubscription(string $purchaseToken, string $subscriptionId) : array
    {
        $response = $this->getHttpClient()->request('POST', $this->buildServiceUri($this->getRegion()),
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
                        HuaweiClient::makeAuthorizationHeaders($this->getAccessToken())
                    )
            ]
        );

        $response = new HuaweiResponseReader($response);

        if ($response->isOk() === false) {
            throw new \BadMethodCallException("invalid request to orders");
        }

        return $response->toMap();
    }
}
