# Architecture

## Goals

Keep OMS/ERP code free of carrier-specific enums and HTTP shapes. ShipBridge owns:

1. A stable `CarrierDriver` contract
2. Request/response DTOs
3. Status normalization into `ShipmentStatus`
4. Driver resolution (config + `extend`)

## Driver model

```
App → ShipBridgeManager → CarrierDriver
                              ├─ FakeCarrierDriver (tests/demo)
                              ├─ HttpCarrierDriver (generic JSON)
                              └─ Custom drivers via extend()
```

`HttpCarrierDriver` documents a minimal REST shape so adapters and fakes share the same contract without shipping vendor SDKs in v0.1.

## Returns / exchanges

Return and exchange are separate driver methods. That forces adapters to map to carrier-specific APIs (or compose create+cancel) instead of pretending every carrier only supports forward shipments.

## Trade-offs

- **No Integrator dependency yet.** Laravel HTTP client is enough for the generic driver; resilient middleware can wrap custom drivers later without coupling this package to `hekal/integrator`.
- **Unknown statuses → Exception.** Prefer soft-fail for webhook ingestion over hard failures on novel vendor codes.
- **Fake driver is stateful per process.** Suitable for tests; not a multi-worker store.
