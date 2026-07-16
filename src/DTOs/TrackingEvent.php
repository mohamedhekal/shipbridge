<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\DTOs;

use Hekal\ShipBridge\Enums\ShipmentStatus;

final readonly class TrackingEvent
{
    public function __construct(
        public ShipmentStatus $status,
        public string $description,
        public ?string $occurredAt = null,
        public ?string $location = null,
    ) {}
}
