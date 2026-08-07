<?php

namespace App\Service;

enum EmployeeSort : string {
    case FULL_NAME = 'full name';
    case EMAIL = 'email';
    case PHONE = 'phone';
    case WEB = 'web';
    case ROOM = 'room';

    public static function values(): array
    {
        return array_map(fn(EmployeeSort $employeeSort) => $employeeSort->value, self::cases());
    }
}
