<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\Drivers;

use Hekal\ShipBridge\Contracts\CarrierDriver;
use Hekal\ShipBridge\DTOs\CreateShipmentRequest;
use Hekal\ShipBridge\DTOs\ExchangeShipmentRequest;
use Hekal\ShipBridge\DTOs\LabelResult;
use Hekal\ShipBridge\DTOs\ReturnShipmentRequest;
use Hekal\ShipBridge\DTOs\ShipmentResult;
use Hekal\ShipBridge\DTOs\TrackingEvent;
use Hekal\ShipBridge\DTOs\TrackingResult;
use Hekal\ShipBridge\Enums\LabelFormat;
use Hekal\ShipBridge\Enums\ShipmentStatus;
use Hekal\ShipBridge\Exceptions\ShipBridgeException;

final class FakeCarrierDriver implements CarrierDriver
{
    /** @var array<string, array<string, mixed>> */
    private array $byId = [];

    /** @var array<string, string> */
    private array $idByTracking = [];

    private int $sequence = 0;

    public function createShipment(CreateShipmentRequest $request): ShipmentResult
    {
        return $this->store(
            status: ShipmentStatus::Created,
            reference: $request->reference,
            payload: $request->toArray(),
        );
    }

    public function track(string $trackingNumber): TrackingResult
    {
        $shipment = $this->findByTracking($trackingNumber);

        /** @var list<TrackingEvent> $events */
        $events = $shipment['events'];

        return new TrackingResult(
            trackingNumber: $trackingNumber,
            status: $shipment['status'],
            events: $events,
            raw: $shipment,
        );
    }

    public function label(string $shipmentId, LabelFormat $format = LabelFormat::Pdf): LabelResult
    {
        $shipment = $this->findById($shipmentId);
        $shipment['status'] = ShipmentStatus::Labeled;
        $shipment['events'][] = new TrackingEvent(
            status: ShipmentStatus::Labeled,
            description: 'Label generated',
            occurredAt: now()->toIso8601String(),
        );
        $this->byId[$shipmentId] = $shipment;

        $body = match ($format) {
            LabelFormat::Pdf => '%PDF-1.4 fake-label',
            LabelFormat::Zpl => '^XA^FDFAKE^XZ',
            LabelFormat::Png => 'PNG',
        };

        return new LabelResult(
            shipmentId: $shipmentId,
            format: $format,
            contents: base64_encode($body),
            base64Encoded: true,
            url: "https://shipbridge.test/labels/{$shipmentId}.{$format->value}",
        );
    }

    public function createReturn(ReturnShipmentRequest $request): ShipmentResult
    {
        $this->findById($request->originalShipmentId);

        return $this->store(
            status: ShipmentStatus::Returned,
            reference: $request->reason,
            payload: $request->toArray(),
            prefix: 'RET',
        );
    }

    public function createExchange(ExchangeShipmentRequest $request): ShipmentResult
    {
        $this->findById($request->originalShipmentId);

        return $this->store(
            status: ShipmentStatus::Exchanged,
            reference: $request->reason,
            payload: $request->toArray(),
            prefix: 'EXC',
        );
    }

    public function advance(string $trackingNumber, ShipmentStatus $status, string $description = ''): void
    {
        $id = $this->idByTracking[$trackingNumber] ?? null;
        if ($id === null) {
            throw ShipBridgeException::shipmentNotFound($trackingNumber);
        }

        $shipment = $this->byId[$id];
        $shipment['status'] = $status;
        $shipment['events'][] = new TrackingEvent(
            status: $status,
            description: $description !== '' ? $description : $status->value,
            occurredAt: now()->toIso8601String(),
        );
        $this->byId[$id] = $shipment;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function store(
        ShipmentStatus $status,
        ?string $reference,
        array $payload,
        string $prefix = 'SHP',
    ): ShipmentResult {
        $this->sequence++;
        $id = sprintf('%s-%04d', $prefix, $this->sequence);
        $tracking = sprintf('%s%s', $prefix, str_pad((string) $this->sequence, 8, '0', STR_PAD_LEFT));

        $event = new TrackingEvent(
            status: $status,
            description: 'Shipment recorded',
            occurredAt: now()->toIso8601String(),
        );

        $this->byId[$id] = [
            'id' => $id,
            'tracking_number' => $tracking,
            'status' => $status,
            'reference' => $reference,
            'payload' => $payload,
            'events' => [$event],
        ];
        $this->idByTracking[$tracking] = $id;

        return new ShipmentResult(
            id: $id,
            trackingNumber: $tracking,
            status: $status,
            carrier: 'fake',
            raw: $this->byId[$id],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function findById(string $shipmentId): array
    {
        if (! isset($this->byId[$shipmentId])) {
            throw ShipBridgeException::shipmentNotFound($shipmentId);
        }

        return $this->byId[$shipmentId];
    }

    /**
     * @return array<string, mixed>
     */
    private function findByTracking(string $trackingNumber): array
    {
        $id = $this->idByTracking[$trackingNumber] ?? null;
        if ($id === null) {
            throw ShipBridgeException::shipmentNotFound($trackingNumber);
        }

        return $this->byId[$id];
    }
}
