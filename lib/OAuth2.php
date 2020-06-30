<?php

namespace zikwall\huawei_api;

use GuzzleHttp\Psr7;
use Psr\Http\Message\UriInterface;

class OAuth2
{
    /**
     * @var array
     */
    private $config = [];
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
    public static $knownGrantTypes = array(
        'authorization_code',
        'refresh_token',
        'password',
        'client_credentials',
    );

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
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

        $this->setAuthorizationUri($this->config['authorizationUri']);
        $this->setTokenCredentialUri($this->config['tokenCredentialUri']);
        $this->setRedirectUri($this->config['redirectUri']);
        $this->setClientId($this->config['clientId']);
        $this->setClientSecret($this->config['clientSecret']);
        $this->setScope($this->config['scope']);

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

    private function coerceUri($uri)
    {
        if (is_null($uri)) {
            return;
        }

        return Psr7\uri_for($uri);
    }

    // setters/getters

    public function getState() : string
    {
        return $this->config['state'];
    }

    public function setState(string $state) : void
    {
        $this->config['state'] = $state;
    }

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

    public function getScope() : string
    {
        if (is_null($this->config['scope'])) {
            return $this->config['scope'];
        }

        return implode(' ', $this->config['scope']);
    }

    public function setScope($scope) : void
    {
        if (is_null($scope)) {
            $this->config['scope'] = null;
        } elseif (is_string($scope)) {
            $this->config['scope'] = explode(' ', $scope);
        } elseif (is_array($scope)) {
            foreach ($scope as $s) {
                $pos = strpos($s, ' ');
                if ($pos !== false) {
                    throw new \InvalidArgumentException(
                        'array scope values should not contain spaces'
                    );
                }
            }
            $this->config['scope'] = $scope;
        } else {
            throw new \InvalidArgumentException(
                'scopes should be a string or array of strings'
            );
        }
    }
}