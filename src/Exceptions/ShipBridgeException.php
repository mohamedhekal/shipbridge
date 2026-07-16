<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\Exceptions;

use RuntimeException;

final class ShipBridgeException extends RuntimeException
{
    public static function unknownDriver(string $name): self
    {
        return new self("Unknown ShipBridge driver [{$name}].");
    }

    public static function unsupportedDriver(string $name): self
    {
        return new self("Unsupported ShipBridge driver type [{$name}].");
    }

    public static function shipmentNotFound(string $id): self
    {
        return new self("Shipment [{$id}] was not found.");
    }

    public static function carrierFailed(string $message, int $status = 0): self
    {
        $suffix = $status > 0 ? " (HTTP {$status})" : '';

        return new self("Carrier request failed{$suffix}: {$message}");
    }
}
