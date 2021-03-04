<?php

namespace Kielabokkie\UberCache\Tests;

use Illuminate\Support\Facades\Cache;
use Kielabokkie\UberCache\Exceptions\UberCacheException;
use Kielabokkie\UberCache\UberCache;
use RachidLaasri\Travel\Travel;

/**
 * phpcs:disable PSR1.Methods.CamelCapsMethodName
 */
class UberCacheTest extends TestCase
{
    /** @test */
    public function retrieves_value_from_cache(): void
    {
        UberCache::remember('test', now()->addMinute(), now()->addHour(), function () {
            return 11;
        });

        $cacheValue = Cache::get('test');
        self::assertEquals(11, $cacheValue);

        UberCache::remember('test', now()->addMinute(), now()->addHour(), function () {
            return 22;
        });

        $cacheValue = Cache::get('test');
        self::assertEquals(11, $cacheValue);

        Travel::to(now()->addMinutes(2));

        UberCache::remember('test', now()->addSeconds(1), now()->addHour(), function () {
            return 33;
        });

        $cacheValue = Cache::get('test');
        self::assertEquals(33, $cacheValue);
    }

    /** @test */
    public function get_from_cache_on_exception(): void
    {
        $key = 'test2';
        $expireAt = now()->addHour();

        UberCache::remember($key, now()->addMinute(), $expireAt, function () {
            return 11;
        });

        $cacheValue = Cache::get($key);
        self::assertEquals(11, $cacheValue);

        Travel::to(now()->addMinutes(5));

        // Outside of regular ttl but exception thrown so get value from cache
        UberCache::remember($key, now()->addMinute(), now()->addHour(), function () {
            throw new \Exception('fail 1');
        });

        $cacheValue = Cache::get($key);
        self::assertEquals(11, $cacheValue);

        Travel::to(now()->addHours(2));

        // Outside of regular ttl and cache expiry time so exception is thrown
        $this->expectException(\Exception::class);

        $exceptionMessage = sprintf(
            'Cache for key "%s" expired before it could be refreshed.',
            $key
        );

        $this->expectExceptionMessage($exceptionMessage);

        UberCache::remember($key, now()->addMinute(), now()->addHour(), function () {
            throw new \Exception('fail 2');
        });
    }

    /** @test */
    public function no_keys_are_left_behind_in_the_cache(): void
    {
        $key = 'test3';

        UberCache::remember($key, now()->addMinute(), now()->addHour(), function () {
            return 11;
        });

        $cacheValue = Cache::get($key);
        self::assertEquals(11, $cacheValue);

        Travel::to(now()->addHours(2));

        // Outside of regular ttl and cache expiry time so exception is thrown
        $this->expectException(UberCacheException::class);

        try {
            UberCache::remember($key, now()->addMinute(), now()->addHour(), function () {
                throw new \Exception('fail');
            });
        } finally {
            self::assertNull(Cache::get(\sprintf('%s:cachedAt', $key)));
            self::assertNull(Cache::get(\sprintf('%s:expireAt', $key)));
        }
    }
}
