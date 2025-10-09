<?php

namespace App\Enums;

enum DeliveryMethod: int
{
    case STORE = 1;
    case DELIVERY = 2;

    public function label(): string
    {
        return match($this) {
            self::STORE => 'Store Pickup',
            self::DELIVERY => 'Delivery',
        };
    }
}
