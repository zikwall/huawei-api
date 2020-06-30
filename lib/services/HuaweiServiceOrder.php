<?php

namespace zikwall\huawei_api\services;

use zikwall\huawei_api\http\HttpClient;
use zikwall\huawei_api\HuaweiClient;
use zikwall\huawei_api\utils\HuaweiResponseReader;
use zikwall\huawei_api\utils\HuaweiRegion;

class HuaweiServiceOrder extends HuaweiServiceBase
{
    use HttpClient;

    /**
     * @var string
     */
    protected $name = 'orders';
    /**
     * @var string
     */
    protected $url = 'applications/purchases/tokens/verify';
    /**
     * @var string
     */
    private $accessToken = '';
    /**
     * @var string
     */
    private $region = HuaweiClient::DEFAULT_REGION;

    public function __construct(string $accessToken, string $region = '')
    {
        $this->accessToken = $accessToken;

        if ($region) {
            if(HuaweiRegion::isAvailable($region)) {
                $this->region = $region;
            }
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
        $response = $this->getHttpClient()->request('POST', $this->buildServiceUri($this->region),
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
                        HuaweiClient::makeAuthorizationHeaders($this->accessToken)
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
