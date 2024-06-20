<?php

namespace App\Enums;

enum StatusClientUserEnum: string
{
    case ACTIVE = 'active';
    case BLOCKED = 'blocked';

    public function toString(): ?string
    {
        return match ($this) {
            self::ACTIVE => __('moonshine::ui.resource.active'),
            self::BLOCKED => __('moonshine::ui.resource.blocked')
        };
    }
}
