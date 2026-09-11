<?php

namespace App\Modules\Departamentos\Models;

use App\Modules\Fases\Models\Fase;
use App\Modules\Inscripciones\Models\Inscripcion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    public $timestamps = false;

    protected $table = 'departamentos';

    protected $fillable = ['nombre'];

    public function fases(): HasMany
    {
        return $this->hasMany(Fase::class);
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'id_departamento');
    }
}
