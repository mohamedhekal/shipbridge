<?php

declare(strict_types=1);

namespace Hekal\ShipBridge;

use Closure;
use Hekal\ShipBridge\Contracts\CarrierDriver;
use Hekal\ShipBridge\Drivers\FakeCarrierDriver;
use Hekal\ShipBridge\Drivers\HttpCarrierDriver;
use Hekal\ShipBridge\DTOs\CreateShipmentRequest;
use Hekal\ShipBridge\DTOs\ExchangeShipmentRequest;
use Hekal\ShipBridge\DTOs\LabelResult;
use Hekal\ShipBridge\DTOs\ReturnShipmentRequest;
use Hekal\ShipBridge\DTOs\ShipmentResult;
use Hekal\ShipBridge\DTOs\TrackingResult;
use Hekal\ShipBridge\Enums\LabelFormat;
use Hekal\ShipBridge\Exceptions\ShipBridgeException;
use Hekal\ShipBridge\Support\StatusNormalizer;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Client\Factory as HttpFactory;

final class ShipBridgeManager
{
    /** @var array<string, CarrierDriver> */
    private array $resolved = [];

    /** @var array<string, Closure(Container, array<string, mixed>): CarrierDriver> */
    private array $customCreators = [];

    public function __construct(
        private readonly Container $container,
    ) {}

    public function driver(?string $name = null): CarrierDriver
    {
        $name ??= (string) config('shipbridge.default', 'fake');

        return $this->resolved[$name] ??= $this->resolve($name);
    }

    /**
     * @param  Closure(Container, array<string, mixed>): CarrierDriver  $callback
     */
    public function extend(string $name, Closure $callback): void
    {
        $this->customCreators[$name] = $callback;
        unset($this->resolved[$name]);
    }

    public function createShipment(CreateShipmentRequest $request, ?string $driver = null): ShipmentResult
    {
        return $this->driver($driver)->createShipment($request);
    }

    public function track(string $trackingNumber, ?string $driver = null): TrackingResult
    {
        return $this->driver($driver)->track($trackingNumber);
    }

    public function label(string $shipmentId, LabelFormat $format = LabelFormat::Pdf, ?string $driver = null): LabelResult
    {
        return $this->driver($driver)->label($shipmentId, $format);
    }

    public function createReturn(ReturnShipmentRequest $request, ?string $driver = null): ShipmentResult
    {
        return $this->driver($driver)->createReturn($request);
    }

    public function createExchange(ExchangeShipmentRequest $request, ?string $driver = null): ShipmentResult
    {
        return $this->driver($driver)->createExchange($request);
    }

    private function resolve(string $name): CarrierDriver
    {
        /** @var array<string, mixed>|null $config */
        $config = config("shipbridge.drivers.{$name}");

        if (! is_array($config)) {
            throw ShipBridgeException::unknownDriver($name);
        }

        $type = (string) ($config['driver'] ?? $name);

        if (isset($this->customCreators[$type])) {
            return ($this->customCreators[$type])($this->container, $config);
        }

        return match ($type) {
            'fake' => new FakeCarrierDriver,
            'http' => new HttpCarrierDriver(
                http: $this->container->make(HttpFactory::class),
                normalizer: $this->normalizerFor($config),
                config: $config,
            ),
            default => throw ShipBridgeException::unsupportedDriver($type),
        };
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function normalizerFor(array $config): StatusNormalizer
    {
        /** @var array<string, string> $driverMap */
        $driverMap = [];
        foreach ((array) ($config['status_map'] ?? []) as $from => $to) {
            if (is_string($from) && is_string($to)) {
                $driverMap[$from] = $to;
                $driverMap[strtolower($from)] = $to;
            }
        }

        /** @var array<string, string> $aliases */
        $aliases = [];
        foreach ((array) config('shipbridge.status_aliases', []) as $from => $to) {
            if (is_string($from) && is_string($to)) {
                $aliases[$from] = $to;
                $aliases[strtolower($from)] = $to;
            }
        }

        return new StatusNormalizer(array_merge($aliases, $driverMap));
    }
}
