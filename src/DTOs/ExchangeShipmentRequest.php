<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\DTOs;

final readonly class ExchangeShipmentRequest
{
    /**
     * @param  list<Parcel>  $outboundParcels
     * @param  list<Parcel>|null  $inboundParcels
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $originalShipmentId,
        public Address $origin,
        public Address $destination,
        public array $outboundParcels,
        public ?array $inboundParcels = null,
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
            'origin' => $this->origin->toArray(),
            'destination' => $this->destination->toArray(),
            'outbound_parcels' => array_map(static fn (Parcel $p): array => $p->toArray(), $this->outboundParcels),
            'inbound_parcels' => $this->inboundParcels === null
                ? null
                : array_map(static fn (Parcel $p): array => $p->toArray(), $this->inboundParcels),
            'reason' => $this->reason,
            'metadata' => $this->metadata,
        ];
    }
}
