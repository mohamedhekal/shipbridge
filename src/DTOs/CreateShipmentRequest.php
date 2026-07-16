<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\DTOs;

final readonly class CreateShipmentRequest
{
    /**
     * @param  list<Parcel>  $parcels
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public Address $origin,
        public Address $destination,
        public array $parcels,
        public ?string $reference = null,
        public array $metadata = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'origin' => $this->origin->toArray(),
            'destination' => $this->destination->toArray(),
            'parcels' => array_map(static fn (Parcel $p): array => $p->toArray(), $this->parcels),
            'reference' => $this->reference,
            'metadata' => $this->metadata,
        ];
    }
}
