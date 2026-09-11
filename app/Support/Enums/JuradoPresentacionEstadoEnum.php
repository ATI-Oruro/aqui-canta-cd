<?php

namespace App\Support\Enums;

enum JuradoPresentacionEstadoEnum: string
{
    case ASIGNADO = 'ASIGNADO';

    public function label(): string
    {
        return match ($this) {
            self::ASIGNADO => 'Asignado',
        };
    }
}