<?php

namespace zikwall\huawei_api;

use GuzzleHttp\Psr7;
use Psr\Http\Message\UriInterface;
use zikwall\huawei_api\utils\Uriable;

class HuaweiOAuth2
{
    use HuaweiApiConfigurable;
    use Uriable;

    /**
     * @var UriInterface
     */
    private $authorizationUri;
    /**
     * @var UriInterface
     */
    private $tokenCredentialUri;
    /**
     * The well known grant types.
     *
     * @var array
     */
    public static $knownGrantTypes = [
        'authorization_code',
        'refresh_token',
        'password',
        'client_credentials',
    ];

    public function __construct(array $config = [])
    {
        $config = array_merge([
            'authorizationUri' => '',
            'tokenCredentialUri' => '',
            'redirectUri' => '',
            'username' => '',
            'password' => '',
            'clientId' => '',
            'clientSecret' => '',
            'scope' => '',
            'state' => ''
        ], $config);

        $this->setAuthorizationUri($config['authorizationUri']);
        $this->setTokenCredentialUri($config['tokenCredentialUri']);
        $this->setRedirectUri($config['redirectUri']);
        $this->setClientId($config['clientId']);
        $this->setClientSecret($config['clientSecret']);
        $this->setScope($config['scope']);

        // TODO
        //$this->setUsername($opts['username']);
        //$this->setPassword($opts['password']);
    }

    public function buildFullAuthorizationUri(array $config = []) : string
    {
        if (is_null($this->getAuthorizationUri())) {
            throw new \InvalidArgumentException(
                'requires an authorizationUri to have been set'
            );
        }

        $params = array_merge([
            'response_type' => 'code',
            'access_type'   => 'offline',
            'client_id'     => $this->getClientId(),
            'redirect_uri'  => $this->getRedirectUri(),
            'state'         => $this->getState(),
            'scope'         => $this->getScope(),
        ], $config);

        if (!empty($params['prompt']) && !empty($params['approval_prompt'])) {
            throw new \InvalidArgumentException(
                'prompt and approval_prompt are mutually exclusive'
            );
        }
        $result = clone $this->authorizationUri;
        $existingParams = Psr7\parse_query($result->getQuery());
        $result = $result->withQuery(
            Psr7\build_query(array_merge($existingParams, $params))
        );

        if ($result->getScheme() != 'https') {
            throw new \InvalidArgumentException(
                'Authorization endpoint must be protected by TLS'
            );
        }

        return $result;
    }

    // get/set

    public function setTokenCredentialUri($uri)
    {
        $this->tokenCredentialUri = $this->coerceUri($uri);
    }

    public function getTokenCredentialUri()
    {
        return $this->tokenCredentialUri;
    }

    public function setAuthorizationUri(string $uri) : void
    {
        $this->authorizationUri = $this->coerceUri($uri);
    }

    public function getAuthorizationUri() : UriInterface
    {
        return $this->authorizationUri;
    }
}
