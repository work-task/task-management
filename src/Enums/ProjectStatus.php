<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in-progress';
    case Completed = 'completed';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(fn (self $value) => $value->value, self::cases());
    }
}
