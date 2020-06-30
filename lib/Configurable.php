<?php

namespace zikwall\huawei_api;

trait Configurable
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

    public function setConfigProperty(string $key, string $value) : void
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
}