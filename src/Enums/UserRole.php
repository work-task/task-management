<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case RoleUser = 'ROLE_USER';
    case RoleAdmin = 'ROLE_ADMIN';
}
