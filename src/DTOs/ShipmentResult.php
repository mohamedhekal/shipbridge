<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\DTOs;

use Hekal\ShipBridge\Enums\ShipmentStatus;

final readonly class ShipmentResult
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public string $id,
        public string $trackingNumber,
        public ShipmentStatus $status,
        public ?string $carrier = null,
        public ?string $labelUrl = null,
        public array $raw = [],
    ) {}
}
