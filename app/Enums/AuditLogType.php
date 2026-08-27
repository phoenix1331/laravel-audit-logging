<?php

declare(strict_types=1);

namespace App\Enums;

enum AuditLogType: string
{
    case USER = 'USER';
    case POST = 'POST';
    case LMS_MODULE = 'LMS_MODULE';
    case LMS_UNIT = 'LMS_UNIT';
    case EVENT = 'EVENT';
    case ORGANISATION = 'ORGANISATION';
}
