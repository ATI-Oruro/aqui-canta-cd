<?php

namespace App\Modules\Departamentos\Models;

use App\Modules\Fases\Models\Fase;
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
}
