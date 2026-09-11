<?php

namespace App\Modules\Participantes\Models;

use App\Modules\Inscripciones\Models\Inscripcion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Participante extends Model
{
    protected $fillable = ['nombre', 'paterno', 'materno', 'pseudonimo'];

    public function inscripciones(): BelongsToMany
    {
        return $this->belongsToMany(
            Inscripcion::class,
            'inscripcion_participante',
            'id_participante',
            'id_inscripcion'
        );
    }

    public function nombreCompleto(): string
    {
        return trim(implode(' ', array_filter([
            $this->nombre,
            $this->paterno,
            $this->materno,
        ])));
    }
}
