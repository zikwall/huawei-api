<?php

namespace zikwall\huawei_api\services;

use zikwall\huawei_api\http\HttpClient;
use zikwall\huawei_api\HuaweiApiConfigurable;
use zikwall\huawei_api\HuaweiClient;
use zikwall\huawei_api\utils\HuaweiResponseReader;
use zikwall\huawei_api\utils\HuaweiRegion;

class HuaweiServiceOrder extends HuaweiServiceBase
{
    use HttpClient;
    use HuaweiApiConfigurable;

    /**
     * @var string
     */
    protected $name = 'orders';
    /**
     * @var string
     */
    protected $url = 'applications/purchases/tokens/verify';


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
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verifyToken(string $productId, string $purchaseToken) : array
    {
        $response = $this->getHttpClient()->request('POST', $this->buildServiceUri($this->getRegion()),
            [
                'body' => json_encode([
                    'purchaseToken' => $purchaseToken,
                    'productId'     => $productId,
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
