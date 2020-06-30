<?php

namespace zikwall\huawei_api;

trait HuaweiApiConfigurable
{
    /**
     * @var array
     */
    private $config = [];

    public function setConfiguration(array $config) : void
    {
        $this->config = $config;
    }

    public function hasConfigProperty(string $key) : bool
    {
        if (!isset($this->config[$key])) {
            return false;
        }

        if (is_null($this->config[$key]) || empty($this->config[$key])) {
            return false;
        }

        return true;
    }

    public function removeConfigProperty(string $key) : bool
    {
        if ($this->hasConfigProperty($key) === false) {
            return false;
        }

        unset($this->config[$key]);
        return true;
    }

    public function getConfigProperty(string $key) : string
    {
        if ($this->hasConfigProperty($key) === false) {
            return '';
        }

        return $this->config[$key];
    }

    public function setConfigProperty(string $key, $value) : void
    {
        $this->config[$key] = $value;
    }

    // getters/setters

    public function setClientId(string $id) : void
    {
        $this->setConfigProperty('client_id', $id);
    }

    public function setClientSecret(string $secret) : void
    {
        $this->setConfigProperty('client_secret', $secret);
    }

    public function setRedirectUri(string $uri) : void
    {
        $this->setConfigProperty('redirect_uri', $uri);
    }

    public function setProductId(string $product) : void
    {
        $this->setConfigProperty('product_id', $product);
    }

    public function getClientId() : string
    {
        return $this->getConfigProperty('client_id');
    }

    public function getClientSecret() : string
    {
        return $this->getConfigProperty('client_secret');
    }

    public function getRedirectUri() : string
    {
        return $this->getConfigProperty('redirect_uri');
    }

    public function getProductId() : string
    {
        return $this->getConfigProperty('product_id');
    }

    // OAuth2

    public function getState() : string
    {
        return $this->getConfigProperty('state');
    }

    public function setState(string $state) : void
    {
        $this->setConfigProperty('state', $state);
    }

    public function getScope() : string
    {
        if ($this->hasConfigProperty('scope') === false) {
            return $this->getConfigProperty('scope');
        }

        return implode(' ', $this->getConfigProperty('scope'));
    }

    public function setScope($scope) : void
    {
        if (is_null($scope)) {
            $this->setConfigProperty('scope', null);
        } elseif (is_string($scope)) {
            $this->setConfigProperty('scope', explode(' ', $scope));
        } elseif (is_array($scope)) {
            foreach ($scope as $s) {
                $pos = strpos($s, ' ');
                if ($pos !== false) {
                    throw new \InvalidArgumentException(
                        'array scope values should not contain spaces'
                    );
                }
            }
            $this->setConfigProperty('scope', $scope);
        } else {
            throw new \InvalidArgumentException(
                'scopes should be a string or array of strings'
            );
        }
    }
}
