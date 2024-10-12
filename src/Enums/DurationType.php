<?php

declare(strict_types=1);

namespace App\Enums;

enum DurationType: string
{
    case Minutes = 'minutes';
    case Hours = 'hours';
    case Days = 'days';
}
