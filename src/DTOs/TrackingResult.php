<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\DTOs;

use Hekal\ShipBridge\Enums\ShipmentStatus;

final readonly class TrackingResult
{
    /**
     * @param  list<TrackingEvent>  $events
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public string $trackingNumber,
        public ShipmentStatus $status,
        public array $events = [],
        public array $raw = [],
    ) {}
}
