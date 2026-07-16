<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\Enums;

enum LabelFormat: string
{
    case Pdf = 'pdf';
    case Zpl = 'zpl';
    case Png = 'png';
}
