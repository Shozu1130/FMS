<?php

namespace App\Enums;

enum Department: string
{
    case BSIT = 'BSIT';
    case BSHM = 'BSHM';
    case BSAIS = 'BSAIS';
    case BSTM = 'BSTM';
    case BSOA = 'BSOA';
    case BSENTREP = 'BSENTREP';
    case BSBA = 'BSBA';
    case BLIS = 'BLIS';
    case BSCpE = 'BSCpE';
    case BSP = 'BSP';
    case BSCRIM = 'BSCRIM';
    case BPED = 'BPED';
    case BTLED = 'BTLED';
    case BEED = 'BEED';
    case BSED = 'BSED';
    case MASTER_ADMIN = 'MASTER ADMIN';

    public static function getAll(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->value;
        }
        return $options;
    }

    public function getFullName(): string
    {
        return match($this) {
            self::BSIT => 'Bachelor of Science in Information Technology',
            self::BSHM => 'Bachelor of Science in Hospitality Management',
            self::BSAIS => 'Bachelor of Science in Accounting Information System',
            self::BSTM => 'Bachelor of Science in Tourism Management',
            self::BSOA => 'Bachelor of Science in Office Administration',
            self::BSENTREP => 'Bachelor of Science in Entrepreneurship',
            self::BSBA => 'Bachelor of Science in Business Administration',
            self::BLIS => 'Bachelor of Library and Information Science',
            self::BSCpE => 'Bachelor of Science in Computer Engineering',
            self::BSP => 'Bachelor of Science in Psychology',
            self::BSCRIM => 'Bachelor of Science in Criminology',
            self::BPED => 'Bachelor of Physical Education',
            self::BTLED => 'Bachelor of Technology and Livelihood Education',
            self::BEED => 'Bachelor of Elementary Education',
            self::BSED => 'Bachelor of Secondary Education',
            self::MASTER_ADMIN => 'Master Administrator',
        };
    }
}
