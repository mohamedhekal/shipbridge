<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\DTOs;

final readonly class ReturnShipmentRequest
{
    /**
     * @param  list<Parcel>|null  $parcels
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $originalShipmentId,
        public Address $returnTo,
        public ?Address $pickupFrom = null,
        public ?array $parcels = null,
        public ?string $reason = null,
        public array $metadata = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'original_shipment_id' => $this->originalShipmentId,
            'return_to' => $this->returnTo->toArray(),
            'pickup_from' => $this->pickupFrom?->toArray(),
            'parcels' => $this->parcels === null
                ? null
                : array_map(static fn (Parcel $p): array => $p->toArray(), $this->parcels),
            'reason' => $this->reason,
            'metadata' => $this->metadata,
        ];
    }
}
