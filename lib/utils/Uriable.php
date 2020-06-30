<?php


namespace zikwall\huawei_api\utils;


use GuzzleHttp\Psr7;

trait Uriable
{
    private function coerceUri($uri)
    {
        if (is_null($uri)) {
            return;
        }

        return Psr7\uri_for($uri);
    }
}
