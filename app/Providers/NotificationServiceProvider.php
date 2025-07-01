<?php

namespace App\Providers;

use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\NotificationRepository;
use App\Services\NotificationCacheService;
use App\Services\NotificationService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('notification.cache', function ($app) {
            return new NotificationCacheService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class
        );

        $this->app->bind(
            NotificationService::class,
            function (Application $app) {
                return new NotificationService(
                    $app->make(NotificationRepositoryInterface::class),
                    $app->make('notification.cache')
                );
            }
        );
    }
}
