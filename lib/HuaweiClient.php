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
     * @var HuaweiOAuth2
     */
    private $auth = null;
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

            'state' => '',

            // Other OAuth2 parameters.
            'prompt' => '',
            'access_type' => 'offline',

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
        $credentials = $response->toMap();
        $credentials['created_at'] = time();

        $this->setAccessToken($credentials['access_token']);
        return $credentials;
    }

    public function fetchAccessTokenWithAuthCode(string $code) : array
    {
        if (strlen($code) == 0) {
            throw new \InvalidArgumentException("Invalid code");
        }

        $auth = $this->getOAuth2Service();
        $auth->setCode($code);
        $auth->setRedirectUri($this->getRedirectUri());

        $response = $this->getHttpClient()->post('https://oauth-login.cloud.huawei.com/oauth2/v3/token', [
            'form_params' => [
                "grant_type"    => "authorization_code",
                "client_id"     => $this->getClientId(),
                "client_secret" => $this->getClientSecret(),
                // example
                // "code_verifier" => "123444444dfd4sadfsdwew321454567587658776t896fdfgdscvvbfxdgfdgfdsfasdfsdgd233",
                "redirect_uri"  => $auth->getRedirectUri(),
                "code"          => $auth->getCode()
            ],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        ]);

        $response = new HuaweiResponseReader($response);
        $creds = $response->toMap();

        if ($creds && isset($creds['access_token'])) {
            $creds['created'] = time();
            $this->setAccessToken($creds['access_token']);
        }

        return $creds;
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

    // getters/setters

    public function getOAuth2Service() : HuaweiOAuth2
    {
        if (!($this->auth instanceof HuaweiOAuth2)) {
            $this->auth = $this->makeOAuth2Service();
        }

        return $this->auth;
    }

    public function makeOAuth2Service() : HuaweiOAuth2
    {
        $auth = new HuaweiOAuth2([
            'clientId'              => $this->getClientId(),
            'clientSecret'          => $this->getClientSecret(),
            'authorizationUri'      => HuaweiConstants::OAUTH2_AUTH_URL,
            'tokenCredentialUri'    => HuaweiConstants::OAUTH2_TOKEN_URI,
            'redirectUri'           => $this->getRedirectUri(),
        ]);

        return $auth;
    }

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
