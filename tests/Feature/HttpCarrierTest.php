<?php

declare(strict_types=1);

use Hekal\ShipBridge\DTOs\Address;
use Hekal\ShipBridge\DTOs\CreateShipmentRequest;
use Hekal\ShipBridge\DTOs\Parcel;
use Hekal\ShipBridge\Enums\ShipmentStatus;
use Hekal\ShipBridge\Exceptions\ShipBridgeException;
use Hekal\ShipBridge\Facades\ShipBridge;
use Illuminate\Support\Facades\Http;

it('creates a shipment through the http driver', function () {
    config()->set('shipbridge.default', 'http');
    config()->set('shipbridge.drivers.http.base_url', 'https://carrier.test/v1');
    config()->set('shipbridge.drivers.http.token', 'secret');

    Http::fake([
        'carrier.test/v1/shipments' => Http::response([
            'id' => 'http-1',
            'tracking_number' => 'TRK1',
            'status' => 'CREATED',
            'carrier' => 'demo',
        ], 201),
        'carrier.test/v1/shipments/track/TRK1' => Http::response([
            'tracking_number' => 'TRK1',
            'status' => 'OFD',
            'events' => [
                ['status' => 'PICKED_UP', 'description' => 'Collected', 'occurred_at' => '2026-07-01T10:00:00Z'],
                ['status' => 'OFD', 'description' => 'Out for delivery', 'occurred_at' => '2026-07-02T08:00:00Z'],
            ],
        ]),
    ]);

    $shipment = ShipBridge::createShipment(new CreateShipmentRequest(
        origin: new Address('A', '1 St', 'Cairo', 'EG'),
        destination: new Address('B', '2 St', 'Alex', 'EG'),
        parcels: [new Parcel(weightKg: 2)],
    ));

    expect($shipment->id)->toBe('http-1')
        ->and($shipment->status)->toBe(ShipmentStatus::Created);

    $tracking = ShipBridge::track('TRK1');
    expect($tracking->status)->toBe(ShipmentStatus::OutForDelivery)
        ->and($tracking->events)->toHaveCount(2);

    Http::assertSent(function ($request) {
        return $request->hasHeader('Authorization', 'Bearer secret')
            && $request->url() === 'https://carrier.test/v1/shipments';
    });
});

it('surfaces carrier http errors', function () {
    config()->set('shipbridge.default', 'http');
    config()->set('shipbridge.drivers.http.base_url', 'https://carrier.test/v1');

    Http::fake([
        'carrier.test/v1/shipments' => Http::response(['message' => 'quota exceeded'], 429),
    ]);

    ShipBridge::createShipment(new CreateShipmentRequest(
        origin: new Address('A', '1 St', 'Cairo', 'EG'),
        destination: new Address('B', '2 St', 'Alex', 'EG'),
        parcels: [new Parcel(weightKg: 1)],
    ));
})->throws(ShipBridgeException::class, 'HTTP 429');
