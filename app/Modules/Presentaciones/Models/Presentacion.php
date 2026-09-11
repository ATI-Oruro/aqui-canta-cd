<?php

namespace App\Modules\Presentaciones\Models;

use App\Modules\Inscripciones\Models\Inscripcion;
use App\Modules\Jurados\Models\JuradoPresentacion;
use App\Support\Enums\PresentacionEstadoEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function jurados(): HasMany
    {
        return $this->hasMany(JuradoPresentacion::class, 'id_presentacion');
    }

    public function juradosAsignados(): HasMany
    {
        return $this->jurados()->with('usuario');
    }

    public function nombreMostrado(): string
    {
        return $this->nombre_presentacion
            ?? $this->inscripcion?->participantes()->first()?->nombreCompleto()
            ?? 'Sin nombre';
    }

    public function esPreseleccion(): bool
    {
        return $this->inscripcion?->esPreseleccion() ?? false;
    }

    public function esFinal(): bool
    {
        return $this->inscripcion?->fase?->esFinal() ?? false;
    }

    public function cronometroCorriendo(): bool
    {
        return $this->cronometro_iniciado_at !== null && $this->cronometro_finalizado_at === null;
    }

    public function segundosTranscurridos(): int
    {
        if ($this->cronometro_iniciado_at === null) {
            return 0;
        }

        $fin = $this->cronometro_finalizado_at ?? now();

        return max(0, (int) $this->cronometro_iniciado_at->diffInSeconds($fin));
    }

    public function segundosRestantes(): int
    {
        return max(0, $this->tiempo_maximo * 60 - $this->segundosTranscurridos());
    }
}
