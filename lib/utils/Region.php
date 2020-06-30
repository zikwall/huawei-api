<?php

namespace zikwall\huawei_api\utils;

class Region
{
    const CHINA = 'China';
    const GERMANY = 'Germany';
    const SINGAPORE = 'Singapore';
    const RUSSIA = 'Russia';

    public static function getList() : array
    {
        return [
            static::CHINA,
            static::GERMANY,
            static::SINGAPORE,
            static::RUSSIA
        ];
    }

    public static function isAvailable(string $region) : bool
    {
        return in_array($region, static::getList());
    }
}