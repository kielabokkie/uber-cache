<?php

namespace Kielabokkie\UberCache;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;

class UberCache
{
    /**
     * @param string $key
     * @param Carbon $ttl
     * @param Carbon $maxTtl
     * @param \Closure $callback
     * @return mixed
     * @throws Exception
     */
    public static function remember(string $key, Carbon $ttl, Carbon $maxTtl, \Closure $callback)
    {
        $value = Cache::get($key);
        $cachedAt = Cache::get(\sprintf('%s:cachedAt', $key));

        // No value in cache or no time set, fetch fresh data
        if ($value === null || $cachedAt === null) {
            return static::fetchAndStore($key, $maxTtl, $callback);
        }

        $diffInSeconds = Carbon::now()->diffInRealSeconds($ttl, false);

        // Outside of the cache timeframe, fetch fresh data
        if (Carbon::now()->subSeconds($diffInSeconds)->gte($cachedAt) === true) {
            return static::fetchAndStore($key, $maxTtl, $callback);
        }

        return $value;
    }

    /**
     * @param string $key
     * @param Carbon $maxTtl
     * @param \Closure $callback
     * @return mixed
     * @throws Exception
     */
    private static function fetchAndStore(string $key, Carbon $maxTtl, \Closure $callback)
    {
        try {
            $value = $callback();

            Cache::put($key, $value);
            Cache::put(\sprintf('%s:cachedAt', $key), now());
            Cache::put(\sprintf('%s:expireAt', $key), $maxTtl);

            return $value;
        } catch (Exception $e) {
            $expireAt = Cache::get(\sprintf('%s:expireAt', $key));

            if (Carbon::now()->lessThan($expireAt) === true) {
                return Cache::get($key);
            }

            throw $e;
        }
    }
}