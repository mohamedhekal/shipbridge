# ShipBridge

[![CI](https://github.com/mohamedhekal/shipbridge/actions/workflows/tests.yml/badge.svg)](https://github.com/mohamedhekal/shipbridge/actions)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4.svg)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11%2F12-FF2D20.svg)](https://laravel.com/)
[![Packagist](https://img.shields.io/packagist/v/mohamedhekal/shipbridge.svg)](https://packagist.org/packages/mohamedhekal/shipbridge)

**Search terms:** laravel, shipping, carriers, tracking, labels, returns, logistics, ecommerce, php, laravel-package, courier, fulfillment.

Unified Laravel shipping abstraction: create, track, label, return, and exchange across carrier drivers with normalized statuses.

**شرح عربي بسيط جدًا:** [docs/GUIDE_AR.md](docs/GUIDE_AR.md)

## Installation

```bash
composer require mohamedhekal/shipbridge
php artisan vendor:publish --tag=shipbridge-config
```

Add the carrier you need (separate package per company):

```bash
composer require mohamedhekal/shipbridge-bosta
# or: shipbridge-aramex / shipbridge-fedex / shipbridge-mng / …
```

## Carrier packages

All carrier drivers below ship as **`^0.2`** with real vendor APIs (not scaffolds).

| Carrier | Package | Region | Integration |
|---|---|---|---|
| [Bosta](https://github.com/mohamedhekal/shipbridge-bosta) | `mohamedhekal/shipbridge-bosta` | Egypt | Business API v2 |
| [Aramex](https://github.com/mohamedhekal/shipbridge-aramex) | `mohamedhekal/shipbridge-aramex` | MENA / Global | Shipping SOAP V2 |
| [Mylerz](https://github.com/mohamedhekal/shipbridge-mylerz) | `mohamedhekal/shipbridge-mylerz` | Egypt / MENA | Integration API |
| [Turbo](https://github.com/mohamedhekal/shipbridge-turbo) | `mohamedhekal/shipbridge-turbo` | Egypt | External API |
| [J&T Express](https://github.com/mohamedhekal/shipbridge-jtexpress) | `mohamedhekal/shipbridge-jtexpress` | Egypt | Open Platform |
| [SMSA](https://github.com/mohamedhekal/shipbridge-smsa) | `mohamedhekal/shipbridge-smsa` | KSA / GCC | SECOM SOAP |
| [FedEx](https://github.com/mohamedhekal/shipbridge-fedex) | `mohamedhekal/shipbridge-fedex` | Global | REST Ship/Track |
| [UPS](https://github.com/mohamedhekal/shipbridge-ups) | `mohamedhekal/shipbridge-ups` | Global | REST OAuth2 |
| [DHL Express](https://github.com/mohamedhekal/shipbridge-dhl) | `mohamedhekal/shipbridge-dhl` | Global | MyDHL API |
| [Egypt Post](https://github.com/mohamedhekal/shipbridge-egyptpost) | `mohamedhekal/shipbridge-egyptpost` | Egypt | TrackTrace + partner gateway |
| [MNG Kargo](https://github.com/mohamedhekal/shipbridge-mng) | `mohamedhekal/shipbridge-mng` | Turkey | ApiZone REST (DHL eCommerce TR) |
| [HepsiJet](https://github.com/mohamedhekal/shipbridge-hepsijet) | `mohamedhekal/shipbridge-hepsijet` | Turkey | Integration REST |
| [Yurtiçi Kargo](https://github.com/mohamedhekal/shipbridge-yurtici) | `mohamedhekal/shipbridge-yurtici` | Turkey | ShippingOrder SOAP |
| [Aras Kargo](https://github.com/mohamedhekal/shipbridge-aras) | `mohamedhekal/shipbridge-aras` | Turkey | CustomerWS SOAP |
| [Sürat Kargo](https://github.com/mohamedhekal/shipbridge-surat) | `mohamedhekal/shipbridge-surat` | Turkey | WebServices SOAP |
| [PTT Kargo](https://github.com/mohamedhekal/shipbridge-ptt) | `mohamedhekal/shipbridge-ptt` | Turkey | PttVeriYukleme SOAP |

```bash
composer require mohamedhekal/shipbridge-bosta:^0.2
# Turkey example:
composer require mohamedhekal/shipbridge-mng:^0.2
```

## Quick start

```php
use Hekal\ShipBridge\Facades\ShipBridge;
use Hekal\ShipBridge\DTOs\Address;
use Hekal\ShipBridge\DTOs\CreateShipmentRequest;
use Hekal\ShipBridge\DTOs\Parcel;

$shipment = ShipBridge::driver('bosta')->createShipment(new CreateShipmentRequest(
    origin: new Address('Warehouse', '1 Industrial Rd', 'Cairo', 'EG'),
    destination: new Address('Customer', '12 Nile St', 'Giza', 'EG'),
    parcels: [new Parcel(weightKg: 1.2)],
    reference: 'ORD-42',
));

$label = ShipBridge::driver('bosta')->label($shipment->id);
$tracking = ShipBridge::driver('bosta')->track($shipment->trackingNumber);
```

Default built-in driver is `fake` (in-memory). Use `http` for a generic JSON carrier, or install a carrier package above.

## Returns & exchanges

First-class methods—not afterthoughts:

```php
ShipBridge::createReturn(new ReturnShipmentRequest(...));
ShipBridge::createExchange(new ExchangeShipmentRequest(...));
```

## Status normalization

Carrier strings (`OFD`, `shipped`, …) map to `ShipmentStatus` via `config/shipbridge.php` (`status_map` + `status_aliases`). Unknown values become `exception` rather than throwing, so webhooks stay resilient.

## Limitations

- Each carrier still needs **live credentials** from that company (sandbox vs production differs).
- Labels are opaque bytes/URLs from the driver; no PDF layout engine.
- Rate shopping and live pickup calendars are out of scope.
- Egypt Post has no public create API (Wassalha); create needs a contracted partner gateway — tracking works via official TrackTrace.

## Testing

```bash
composer install && composer test
```

## License

MIT

---

<p align="center">
  <img src="docs/assets/banner.png" alt="ShipBridge — Unified Laravel shipping" width="100%">
</p>
