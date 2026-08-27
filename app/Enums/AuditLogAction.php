<?php

declare(strict_types=1);

namespace App\Enums;

enum AuditLogAction: string
{
    case CREATE = 'CREATE';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
    case ARCHIVE = 'ARCHIVE';
    case LOGIN = 'LOGIN';
    case LOGOUT = 'LOGOUT';
    case PASSWORD_RESET_REQUEST = 'PASSWORD_RESET_REQUEST';
    case PASSWORD_RESET_SUCCESS = 'PASSWORD_RESET_SUCCESS';
}
