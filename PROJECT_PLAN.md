# ShipBridge — Project Plan

## Name

**ShipBridge** (`hekal/shipbridge`)  
Alternatives: `carriergate`, `labelkit`

## Vision

A Laravel-first shipping facade: one API for create / track / label / return / exchange, with carrier drivers and a status normalization layer so OMS code does not leak vendor enums.

## v0.1 scope

- `CarrierDriver` contract + manager/facade
- DTOs for address, parcel, create/return/exchange, shipment/tracking/label results
- Unified `ShipmentStatus` enum + configurable `StatusNormalizer`
- `FakeCarrierDriver` for tests and local demos
- `HttpCarrierDriver` for a generic JSON carrier shape (Http::fake friendly)
- Pest tests, Pint, PHPStan level 6, CI

## Out of v0.1

- Real Aramex / Bosta / FedEx SDKs (register as drivers later)
- Rate shopping UI
- Pickup scheduling against live calendars
- PDF label rendering (drivers return opaque bytes/URLs)
