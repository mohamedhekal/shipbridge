<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\Facades;

use Hekal\ShipBridge\Contracts\CarrierDriver;
use Hekal\ShipBridge\DTOs\CreateShipmentRequest;
use Hekal\ShipBridge\DTOs\ExchangeShipmentRequest;
use Hekal\ShipBridge\DTOs\LabelResult;
use Hekal\ShipBridge\DTOs\ReturnShipmentRequest;
use Hekal\ShipBridge\DTOs\ShipmentResult;
use Hekal\ShipBridge\DTOs\TrackingResult;
use Hekal\ShipBridge\Enums\LabelFormat;
use Hekal\ShipBridge\ShipBridgeManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static CarrierDriver driver(?string $name = null)
 * @method static void extend(string $name, \Closure $callback)
 * @method static ShipmentResult createShipment(CreateShipmentRequest $request, ?string $driver = null)
 * @method static TrackingResult track(string $trackingNumber, ?string $driver = null)
 * @method static LabelResult label(string $shipmentId, LabelFormat $format = LabelFormat::Pdf, ?string $driver = null)
 * @method static ShipmentResult createReturn(ReturnShipmentRequest $request, ?string $driver = null)
 * @method static ShipmentResult createExchange(ExchangeShipmentRequest $request, ?string $driver = null)
 *
 * @see ShipBridgeManager
 */
final class ShipBridge extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ShipBridgeManager::class;
    }
}
