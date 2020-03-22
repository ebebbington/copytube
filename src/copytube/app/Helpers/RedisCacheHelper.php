<?php

/**
 * DO NOT USE THIS CLASS
 *
 * Use `Cache::...` instead.
 *
 * I managed to get Cache working after i created this class, and it is better to use, so this class serves as a
 * reference now
 */

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * @class RedisCacheHelper
 *
 * @package App\Helpers
 *
 * @description
 * Abstracted logic to handle getting, setting, and updating data inside the Redis cache.
 * Mainly because this is database data so we are encoding and decoding the data.
 * To get, update or set, you only need to call the respective method e.g. for updating, don't call `update` and `set`
 */
class RedisCacheHelper {

    /**
     * @method get
     *
     * @description
     * Gets cached data from redis by the key
     *
     * @param string $cacheKey The key for the cached data e.g. 'db:comments:all'
     *
     * @example
     * $cacheKey = 'db:comments:videoTitle=Something More'
     * $data = RedisCacheHelper::get($cacheKey); // false, or the data
     *
     * @return bool|mixed
     */
    public static function get ($cacheKey)
    {
        $loggingPrefix = '[RedisCacheHelper - get]';
        $Redis = Redis::connection();
        $cacheKey = RedisCacheHelper::normaliseCacheKey($cacheKey);
        Log::debug($loggingPrefix . ' Going to get the caches data for the key of: ' . $cacheKey);
        if ($cacheData = $Redis->get($cacheKey)) {
            Log::debug($loggingPrefix . ' Cached data was found, see below:');
            Log::debug($cacheData);
            return json_decode($cacheData);
        } else {
            Log::debug($loggingPrefix . ' No cached data was found for that key');
            return false;
        }
    }

    /**
     * @method update
     *
     * @description
     * Update data in the redis cache by the key.
     * This is specifically used for arrays of data, where we push a new item to it, this will not replace data - to do
     * so, use the `set` method
     *
     * @param mixed $data The data that needs to be added to the cached data
     * @param string $cacheKey The key for the cached data
     *
     * @example
     * // Say someone posted a new comment (scenario where we want to add data to a cache key
     * $cacheKey = 'db:comments:videoTitle=Something More'
     * $data = {...} // e.g. could be a new comment for a video
     * RedisCacheHelper::update($data, $cacheKey);
     */
    public static function update ($data, $cacheKey): void
    {
        $loggingPrefix = '[RedisCacheHelper - update]';
        $cacheKey = RedisCacheHelper::normaliseCacheKey($cacheKey);
        if ($cacheData = RedisCacheHelper::get($cacheKey)) {
            Log::debug($loggingPrefix . ' Cached data exists for ' . $cacheKey . '. Going to update it with:');
            Log::debug($data);
            array_unshift($cacheData, $data);
            RedisCacheHelper::set($cacheData, $cacheKey);
        } else {
            Log::debug($loggingPrefix . ' No cached data was found. Not going to update.');
        }
    }

    /**
     * @method set
     *
     * @description
     * Create or overwrite data inside the redis cache
     *
     * @param mixed $data Whatever data you want to set inside the cache
     * @param string $cacheKey The key to associate this data with
     *
     * @example
     * $data = 'Some value'
     * $cacheKey = 'test'
     * RedisCacheHelper::set($data, $cacheKey);
     */
    public static function set ($data, $cacheKey): void
    {
        $loggingPrefix = '[RedisCacheHelper - set]';
        $Redis = Redis::connection();
        $cacheKey = RedisCacheHelper::normaliseCacheKey($cacheKey);
        Log::debug($loggingPrefix . ' Going to set the below data to ' . $cacheKey . ':');
        Log::debug($data);
        $Redis->set($cacheKey, json_encode($data));
    }

    /**
     * @method normaliseCacheKey
     *
     * @description
     * Just replace the spaces in the cache key. This is because the logic of:
     *     $videoTitle = $request->input('title') // dynamic, could be 'Something More'
     *     $cacheKey = 'db:comments:videoTitle=' . $videoTitle; // 'db:comments:videoTitle=Something More'
     * Dont really want spaces inside a key name.
     * This kind of turns the key name into a HTTP querystring
     *
     * @param string $cacheKey Cache key name to modify
     *
     * @example
     * RedisCacheHelper::normaliseCacheKey('hello world'); // 'hello+world'
     *
     * @return string
     */
    private static function normaliseCacheKey ($cacheKey): string
    {
        return str_replace(' ', '+', $cacheKey);
    }

}
