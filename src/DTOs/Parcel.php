<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\DTOs;

final readonly class Parcel
{
    public function __construct(
        public float $weightKg,
        public ?float $lengthCm = null,
        public ?float $widthCm = null,
        public ?float $heightCm = null,
        public string $description = 'Goods',
        public int $quantity = 1,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'weight_kg' => $this->weightKg,
            'length_cm' => $this->lengthCm,
            'width_cm' => $this->widthCm,
            'height_cm' => $this->heightCm,
            'description' => $this->description,
            'quantity' => $this->quantity,
        ];
    }
}
