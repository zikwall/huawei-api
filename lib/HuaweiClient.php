<?php

namespace zikwall\huawei_api;

use zikwall\huawei_api\http\HttpClient;
use zikwall\huawei_api\utils\Region;

class HuaweiClient
{
    use HttpClient;

    // https://developer.huawei.com/consumer/en/doc/38054564
    const OAUTH2_TOKEN_URI  = 'https://oauth-login.cloud.huawei.com/oauth2/v2/token';
    const OAUTH2_AUTH_URL   = 'https://oauth-login.cloud.huawei.com/oauth2/v2/authorize';

    const DEFAULT_CONFIG_FILE_NAME = 'agconnect-services';
    const DEFAULT_REGION = Region::RUSSIA;

    /**
     * @var array
     */
    private $config = [];
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
    /**
     * @var OAuth2
     */
    private $auth;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
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
            'state' => '',

            // Other OAuth2 parameters.
            'prompt' => '',
            'access_type' => 'offline',
        ], $config);

        if (!is_null($this->config['credentials'])) {
            $this->setAuthConfig($this->config['credentials']);
            unset($this->config['credentials']);
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

        if ($response->getStatusCode() !== 200) {
            throw new \BadMethodCallException("invalid request to access token");
        }

        $credentials = json_decode($response->getBody()->getContents(), true);
        $credentials['created_at'] = time();

        $this->setAccessToken($credentials['access_token']);
        return $credentials;
    }

    public function fetchAccessTokenWithAuthCode(string $code) : array
    {

    }

    public function makeAuthUrl($scope = '') : string
    {
        if (is_array($scope)) {
            $scope = implode(' ', $scope);
        }

        $params = array_filter(
            [
                'access_type' => $this->config['access_type'],
                'prompt' => $this->config['prompt'],
                'response_type' => 'code',
                'scope' => $scope,
                'state' => $this->config['state'],
            ]
        );

        $auth = $this->getOAuth2Service();
        return (string) $auth->buildFullAuthorizationUri($params);
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

    public function getOAuth2Service() : OAuth2
    {
        if (!isset($this->auth)) {
            $this->auth = $this->makeOAuth2Service();
        }

        return $this->auth;
    }

    public function makeOAuth2Service() : OAuth2
    {
        $auth = new OAuth2([
            'clientId'          => $this->getClientId(),
            'clientSecret'      => $this->getClientSecret(),
            'authorizationUri'   => self::OAUTH2_AUTH_URL,
            'tokenCredentialUri' => self::OAUTH2_TOKEN_URI,
            'redirectUri'       => $this->getRedirectUri(),
        ]);

        return $auth;
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

    public function setClientId(string $id) : void
    {
        $this->config['client_id'] = $id;
    }

    public function setClientSecret(string $secret) : void
    {
        $this->config['client_secret'] = $secret;
    }

    public function setRedirectUri(string $uri) : void
    {
        $this->config['redirect_uri'] = $uri;
    }

    public function setProductId(string $product) : void
    {
        $this->config['product_id'] = $product;
    }

    public function getClientId() : string
    {
        return $this->config['client_id'];
    }

    public function getClientSecret() : string
    {
        return $this->config['client_secret'];
    }

    public function getRedirectUri() : string
    {
        return $this->config['redirect_uri'];
    }

    public function getProductId() : string
    {
        return $this->config['product_id'];
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
        // set product id
        // set app id
        // set configuration version
        // set package name
        // set api key
        // set cp id
    }
}