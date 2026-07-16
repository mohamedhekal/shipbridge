<?php

declare(strict_types=1);

namespace Hekal\ShipBridge;

use Illuminate\Support\ServiceProvider;

final class ShipBridgeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/shipbridge.php', 'shipbridge');

        $this->app->singleton(ShipBridgeManager::class, function ($app): ShipBridgeManager {
            return new ShipBridgeManager($app);
        });

        $this->app->alias(ShipBridgeManager::class, 'shipbridge');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/shipbridge.php' => config_path('shipbridge.php'),
            ], 'shipbridge-config');
        }
    }
}
