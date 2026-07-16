# ShipBridge


[![CI](https://github.com/mohamedhekal/shipbridge/actions/workflows/tests.yml/badge.svg)](https://github.com/mohamedhekal/shipbridge/actions)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4.svg)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11%2F12-FF2D20.svg)](https://laravel.com/)

**Search terms:** laravel, shipping, carriers, tracking, labels, returns, logistics, ecommerce, php, laravel-package, courier, fulfillment.


Unified Laravel shipping abstraction: create, track, label, return, and exchange across carrier drivers with normalized statuses.

## Installation

```bash
composer require mohamedhekal/shipbridge
php artisan vendor:publish --tag=shipbridge-config
```

## Quick start

```php
use Hekal\ShipBridge\Facades\ShipBridge;
use Hekal\ShipBridge\DTOs\Address;
use Hekal\ShipBridge\DTOs\CreateShipmentRequest;
use Hekal\ShipBridge\DTOs\Parcel;

$shipment = ShipBridge::createShipment(new CreateShipmentRequest(
    origin: new Address('Warehouse', '1 Industrial Rd', 'Cairo', 'EG'),
    destination: new Address('Customer', '12 Nile St', 'Giza', 'EG'),
    parcels: [new Parcel(weightKg: 1.2)],
    reference: 'ORD-42',
));

$label = ShipBridge::label($shipment->id);
$tracking = ShipBridge::track($shipment->trackingNumber);
```

Default driver is `fake` (in-memory). Point `SHIPBRIDGE_DRIVER=http` at a generic JSON carrier API, or register your own adapter:

```php
ShipBridge::extend('bosta', function ($app, array $config) {
    return new BostaDriver($config);
});
```

## Returns & exchanges

First-class methods—not afterthoughts:

```php
ShipBridge::createReturn(new ReturnShipmentRequest(...));
ShipBridge::createExchange(new ExchangeShipmentRequest(...));
```

## Status normalization

Carrier strings (`OFD`, `shipped`, …) map to `ShipmentStatus` via `config/shipbridge.php` (`status_map` + `status_aliases`). Unknown values become `exception` rather than throwing, so webhooks stay resilient.

## Limitations (v0.1)

- No bundled Aramex/Bosta/FedEx SDKs—implement `CarrierDriver` per vendor.
- Labels are opaque bytes/URLs from the driver; no PDF layout engine.
- Rate shopping and live pickup calendars are out of scope.

## Testing

```bash
composer install && composer test
```

## License

MIT
