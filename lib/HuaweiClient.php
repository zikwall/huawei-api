<?php

namespace zikwall\huawei_api;

use zikwall\huawei_api\http\HttpClient;
use zikwall\huawei_api\utils\Region;
use zikwall\huawei_api\utils\HuaweiResponseReader;

class HuaweiClient
{
    use Configurable;
    use HttpClient;

    const OAUTH2_TOKEN_URI = 'https://oauth-login.cloud.huawei.com/oauth2/v2/token';
    const OAUTH2_AUTH_URL = 'https://oauth-login.cloud.huawei.com/oauth2/v2/auth';

    const DEFAULT_CONFIG_FILE_NAME = 'agconnect-services';
    const DEFAULT_REGION = Region::RUSSIA;

    /**
     * @var string
     */
    private $token = '';
    /**
     * @var array
     */
    private $cache = [];
    /**
     * @var string
     */
    private $region = self::DEFAULT_REGION;

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
        $response = $this->getHttpClient()->request('POST', static::OAUTH2_TOKEN_URI,
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

    public function setRegion(string $region) : void
    {
        if (!Region::isAvailable($region)) {
            throw new \InvalidArgumentException('region is not available');
        }

        $this->region = $region;
    }

    public function getRegion() : string
    {
        return $this->region;
    }

    public function setAccessToken(string $token) : void
    {
        if ($token == null) {
            throw new \InvalidArgumentException('invalid json token');
        }

        $this->token = $token;
    }

    public function getAccessToken() : string
    {
        return $this->token;
    }


    public function setAuthConfigFile(string $file) : void
    {
        $this->setAuthConfig($file);
    }

    public function setAuthConfig($config) : void
    {
        if (is_string($config)) {
            if (!file_exists($config)) {
                throw new \InvalidArgumentException(sprintf('file "%s" does not exist', $config));
            }

            $json = file_get_contents($config);

            if (!$config = json_decode($json, true)) {
                throw new \LogicException('invalid json for auth config');
            }
        }

        if(!isset($config['client'])) {
            throw new \InvalidArgumentException("invalid json content");
        }

        $this->setClientId($config['client']['client_id']);
        $this->setClientSecret($config['client']['client_secret']);
        $this->setProductId($config['client']['product_id']);

        // TODO
        // set app id
        // set configuration version
        // set package name
        // set api key
        // set cp id
    }
}
