<?php

declare(strict_types=1);

namespace Hekal\ShipBridge\Enums;

enum ShipmentStatus: string
{
    case Created = 'created';
    case Labeled = 'labeled';
    case PickedUp = 'picked_up';
    case InTransit = 'in_transit';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case Exception = 'exception';
    case Cancelled = 'cancelled';
    case Returned = 'returned';
    case Exchanged = 'exchanged';
}
