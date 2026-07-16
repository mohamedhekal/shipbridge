<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\DTOs;

use Hekal\ShipBridge\Enums\LabelFormat;

final readonly class LabelResult
{
    public function __construct(
        public string $shipmentId,
        public LabelFormat $format,
        public string $contents,
        public bool $base64Encoded = true,
        public ?string $url = null,
    ) {}
}
