<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in-progress';
    case Completed = 'completed';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(fn (self $status) => $status->value, self::cases());
    }
}
