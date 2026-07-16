<?php

declare(strict_types=1);

use Hekal\ShipBridge\Drivers\FakeCarrierDriver;
use Hekal\ShipBridge\DTOs\Address;
use Hekal\ShipBridge\DTOs\CreateShipmentRequest;
use Hekal\ShipBridge\DTOs\ExchangeShipmentRequest;
use Hekal\ShipBridge\DTOs\Parcel;
use Hekal\ShipBridge\DTOs\ReturnShipmentRequest;
use Hekal\ShipBridge\Enums\LabelFormat;
use Hekal\ShipBridge\Enums\ShipmentStatus;
use Hekal\ShipBridge\Exceptions\ShipBridgeException;
use Hekal\ShipBridge\Facades\ShipBridge;

function sampleAddresses(): array
{
    return [
        new Address('Warehouse', '1 Industrial Rd', 'Cairo', 'EG', phone: '+20100000000'),
        new Address('Customer', '12 Nile St', 'Giza', 'EG', phone: '+20111111111'),
    ];
}

it('creates tracks and labels shipments with the fake driver', function () {
    [$origin, $destination] = sampleAddresses();

    $shipment = ShipBridge::createShipment(new CreateShipmentRequest(
        origin: $origin,
        destination: $destination,
        parcels: [new Parcel(weightKg: 1.5)],
        reference: 'ORD-100',
    ));

    expect($shipment->id)->toStartWith('SHP-')
        ->and($shipment->trackingNumber)->toStartWith('SHP')
        ->and($shipment->status)->toBe(ShipmentStatus::Created)
        ->and($shipment->carrier)->toBe('fake');

    $label = ShipBridge::label($shipment->id, LabelFormat::Pdf);
    expect($label->base64Encoded)->toBeTrue()
        ->and(base64_decode($label->contents))->toContain('PDF');

    /** @var FakeCarrierDriver $driver */
    $driver = ShipBridge::driver('fake');
    $driver->advance($shipment->trackingNumber, ShipmentStatus::InTransit, 'Left hub');

    $tracking = ShipBridge::track($shipment->trackingNumber);
    expect($tracking->status)->toBe(ShipmentStatus::InTransit)
        ->and($tracking->events)->not->toBeEmpty();
});

it('creates return and exchange shipments', function () {
    [$origin, $destination] = sampleAddresses();

    $original = ShipBridge::createShipment(new CreateShipmentRequest(
        origin: $origin,
        destination: $destination,
        parcels: [new Parcel(weightKg: 0.8)],
    ));

    $return = ShipBridge::createReturn(new ReturnShipmentRequest(
        originalShipmentId: $original->id,
        returnTo: $origin,
        pickupFrom: $destination,
        reason: 'wrong size',
    ));

    expect($return->status)->toBe(ShipmentStatus::Returned)
        ->and($return->id)->toStartWith('RET-');

    $exchange = ShipBridge::createExchange(new ExchangeShipmentRequest(
        originalShipmentId: $original->id,
        origin: $origin,
        destination: $destination,
        outboundParcels: [new Parcel(weightKg: 0.8, description: 'Replacement')],
        reason: 'size exchange',
    ));

    expect($exchange->status)->toBe(ShipmentStatus::Exchanged)
        ->and($exchange->id)->toStartWith('EXC-');
});

it('rejects unknown shipments', function () {
    ShipBridge::track('MISSING');
})->throws(ShipBridgeException::class);
