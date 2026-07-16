<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\Support;

use Hekal\ShipBridge\Enums\ShipmentStatus;

final class StatusNormalizer
{
    /**
     * @param  array<string, string>  $map
     */
    public function __construct(
        private readonly array $map = [],
    ) {}

    public function normalize(string $raw): ShipmentStatus
    {
        $key = strtolower(trim($raw));
        $key = str_replace(['-', ' '], '_', $key);

        $mapped = $this->map[$raw]
            ?? $this->map[$key]
            ?? $this->map[strtoupper($raw)]
            ?? $key;

        $value = is_string($mapped) ? strtolower(str_replace(['-', ' '], '_', $mapped)) : $key;

        return ShipmentStatus::tryFrom($value) ?? ShipmentStatus::Exception;
    }
}
