<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\Contracts;

use Hekal\ShipBridge\DTOs\CreateShipmentRequest;
use Hekal\ShipBridge\DTOs\ExchangeShipmentRequest;
use Hekal\ShipBridge\DTOs\LabelResult;
use Hekal\ShipBridge\DTOs\ReturnShipmentRequest;
use Hekal\ShipBridge\DTOs\ShipmentResult;
use Hekal\ShipBridge\DTOs\TrackingResult;
use Hekal\ShipBridge\Enums\LabelFormat;

interface CarrierDriver
{
    public function createShipment(CreateShipmentRequest $request): ShipmentResult;

    public function track(string $trackingNumber): TrackingResult;

    public function label(string $shipmentId, LabelFormat $format = LabelFormat::Pdf): LabelResult;

    public function createReturn(ReturnShipmentRequest $request): ShipmentResult;

    public function createExchange(ExchangeShipmentRequest $request): ShipmentResult;
}
