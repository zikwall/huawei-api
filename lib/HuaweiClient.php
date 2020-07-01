<?php

namespace zikwall\huawei_api;

use zikwall\huawei_api\constants\HuaweiConstants;
use zikwall\huawei_api\http\HttpClient;
use zikwall\huawei_api\utils\HuaweiResponseReader;

class HuaweiClient
{
    use HuaweiApiConfigurable;
    use HttpClient;

    /**
     * @var array
     */
    private $cache = [];

    public function __construct(array $config = [])
    {
        $this->setConfiguration(array_merge([
            'application_name' => '',
            'configuration_version' => '',

            'package_name' => '',
            'app_id' => '',
            'product_id' => '',

            // https://developer.huawei.com/consumer/en/doc/development/HMS-References/iap-obtain-application-level-AT-v4
            'client_id' => '',
            'client_secret' => '',

            // Path to JSON credentials or an array representing those credentials
            // @see HuaweiClient::setAuthConfig
            'credentials' => null,

            'redirect_uri' => '',
        ], $config));

        if (($this->hasConfigProperty('credentials'))) {
            $this->setAuthConfig($this->getConfigProperty('credentials'));
            $this->removeConfigProperty('credentials');
        }

        $this->makeHttpClient();
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchAccessToken() : array
    {
        $response = $this->getHttpClient()->request('POST', HuaweiConstants::OAUTH2_TOKEN_URI,
        [
            'form_params' => [
                'grant_type'    => 'client_credentials',
                'client_secret' => $this->getClientSecret(),
                'client_id'     => $this->getClientId()
            ],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
            ]
        ]);

        $response = new HuaweiResponseReader($response);

        if ($response->isOk() === false) {
            throw new \BadMethodCallException("invalid request to access token");
        }

        $credentials = $response->toMap();
        $credentials['created_at'] = time();

        $this->setAccessToken($credentials['access_token']);
        return $credentials;
    }

    public static function makeAuthorizationHeaders(string $access_token) : array
    {
        $oriString      = sprintf("APPAT:%s", $access_token);
        $authorization  = sprintf("Basic %s", base64_encode(utf8_encode($oriString)));
        $headers = [
            'Authorization' => $authorization,
            'Content-Type'  => 'application/json;charset=UTF-8'
        ];

        return $headers;
    }

    // getters/setters

    public function setAuthConfigFile(string $file) : void
    {
        if (is_string($file)) {
            if (!file_exists($file)) {
                throw new \InvalidArgumentException(sprintf('file "%s" does not exist', $file));
            }

            $json = file_get_contents($file);

            if (!$config = json_decode($json, true)) {
                throw new \LogicException('invalid json for auth config');
            }
        }

        if(!isset($config['client'])) {
            throw new \InvalidArgumentException("invalid json content");
        }

        $this->setAuthConfig($config['client']);
    }

    public function setAuthConfig($config) : void
    {
        $this->setClientId($config['client_id']);
        $this->setClientSecret($config['client_secret']);
        $this->setProductId($config['product_id']);

        // TODO
        // set app id
        // set configuration version
        // set package name
        // set api key
        // set cp id
    }
}
