<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\Tests;

use Hekal\ShipBridge\ShipBridgeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [ShipBridgeServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('shipbridge.default', 'fake');
    }
}
