<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class NotificationCacheService
{
    private const DEFAULT_TTL = 300;
    private const LONG_TTL = 3600;
    private const CACHE_CONTROL_KEY = '_user_notification_cache_keys';

    /**
     * Custom cache remember method
     *
     * @param string $key
     * @param int $ttl
     * @param callable $callback
     * @return mixed
     */
    public function remember(int $userId, string $key, null | int $ttl, callable $callback): mixed
    {
        if (is_null($ttl)) {
            $ttl = self::DEFAULT_TTL;
        }

        $this->addKeyInCacheControlList($userId, $key);

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Generates a cache key for user notifications with pagination and filters.
     *
     * @param int $userId
     * @param int $page
     * @param int $perPageOrTitle
     * @param array $filters
     * @return string
     */
    public function getUserNotificationsCacheKey(
        int $userId,
        int $page,
        int|string $perPageOrTitle,
        array $filters = []
    ): string {
        $filterHash = md5(serialize($filters));
        return "user_notifications_{$userId}_page_{$page}_per_{$perPageOrTitle}_filters_{$filterHash}";
    }

    /**
     * Invalidates the cache for a user's notifications and unread count.
     *
     * @param int $userId
     * @return void
     */
    public function invalidateUserNotificationCache(int $userId): void
    {
        $cacheKeyList = Cache::get($userId . self::CACHE_CONTROL_KEY, []);

        foreach ($cacheKeyList as $cacheKey) {
            Cache::forget($cacheKey);
        }
    }

    /**
     * Adds a key to the cache control list for a user.
     * @param int $userId
     * @param string $key
     * @return void
     */
    private function addKeyInCacheControlList(int $userId, string $key): void
    {
        $cacheControlKey = $userId . self::CACHE_CONTROL_KEY;
        $cacheControl = Cache::get($cacheControlKey, []);
        if (!in_array($key, $cacheControl)) {
            $cacheControl[] = $key;
            Cache::put($cacheControlKey, $cacheControl, self::LONG_TTL);
        }
    }
}
