<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RedisCacheHelper {

    public static function get ($cacheKey)
    {
        $Redis = Redis::connection();
        $cacheKey = RedisCacheHelper::normaliseCacheKey($cacheKey);
        if ($cacheData = $Redis->get($cacheKey)) {
            return json_decode($cacheData);
        } else {
            return false;
        }
    }

    public static function update ($data, $cacheKey): void
    {
        if ($cacheData = RedisCacheHelper::get($cacheKey)) {
            array_unshift($data, $cacheData);
            RedisCacheHelper::set($data, $cacheKey);
        }
    }

    public static function set ($data, $cacheKey): void
    {
        $Redis = Redis::connection();
        $cacheKey = RedisCacheHelper::normaliseCacheKey($cacheKey);
        $Redis->set($cacheKey, json_encode($data));
    }

    private static function normaliseCacheKey ($cacheKey): string
    {
        return str_replace(' ', '+', $cacheKey);
    }

}
