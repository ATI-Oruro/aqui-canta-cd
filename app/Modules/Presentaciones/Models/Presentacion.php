<?php

namespace App\Modules\Presentaciones\Models;

use App\Modules\Inscripciones\Models\Inscripcion;
use App\Support\Enums\PresentacionEstadoEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presentacion extends Model
{
    protected $table = 'presentaciones';

    protected $fillable = [
        'id_inscripcion',
        'nombre_presentacion',
        'estado',
        'tiempo_maximo',
        'hora_inicio',
        'hora_fin',
        'cronometro_iniciado_at',
        'cronometro_finalizado_at',
        'nota_final',
    ];

    protected function casts(): array
    {
        return [
            'estado' => PresentacionEstadoEnum::class,
            'tiempo_maximo' => 'integer',
            'hora_inicio' => 'immutable_datetime:H:i',
            'hora_fin' => 'immutable_datetime:H:i',
            'cronometro_iniciado_at' => 'datetime',
            'cronometro_finalizado_at' => 'datetime',
            'nota_final' => 'decimal:2',
        ];
    }

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion');
    }

    public function nombreMostrado(): string
    {
        return $this->nombre_presentacion
            ?? $this->inscripcion?->participantes()->first()?->nombreCompleto()
            ?? 'Sin nombre';
    }
}
