<?php

declare(strict_types=1);

use Hekal\ShipBridge\Enums\ShipmentStatus;
use Hekal\ShipBridge\Support\StatusNormalizer;

it('normalizes carrier-specific status codes', function () {
    $normalizer = new StatusNormalizer([
        'OFD' => 'out_for_delivery',
        'ofd' => 'out_for_delivery',
        'shipped' => 'in_transit',
    ]);

    expect($normalizer->normalize('OFD'))->toBe(ShipmentStatus::OutForDelivery)
        ->and($normalizer->normalize('shipped'))->toBe(ShipmentStatus::InTransit)
        ->and($normalizer->normalize('delivered'))->toBe(ShipmentStatus::Delivered)
        ->and($normalizer->normalize('weird-unknown'))->toBe(ShipmentStatus::Exception);
});
