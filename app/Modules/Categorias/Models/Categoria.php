<?php

namespace App\Modules\Categorias\Models;

use App\Modules\Inscripciones\Models\Inscripcion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $fillable = ['categoria', 'max_participantes', 'tiempo_presentacion'];

    protected $casts = [
        'max_participantes' => 'integer',
        'tiempo_presentacion' => 'integer',
    ];

    public function categoriaParametros(): HasMany
    {
        return $this->hasMany(CategoriaParametro::class, 'id_categoria');
    }

    public function parametros(): BelongsToMany
    {
        return $this->belongsToMany(Parametro::class, 'categoria_parametro', 'id_categoria', 'id_parametro')
            ->withPivot(['valor_minimo', 'valor_maximo', 'orden'])
            ->orderByPivot('orden');
    }

    public function parametrosArray(): array
    {
        return $this->parametros->map(fn ($p) => [
            'id' => $p->id,
            'parametro' => $p->parametro,
            'valor_minimo' => $p->pivot->valor_minimo,
            'valor_maximo' => $p->pivot->valor_maximo,
            'orden' => $p->pivot->orden,
        ])->toArray();
    }

    public function sumaValorMaximo(): int
    {
        return $this->categoriaParametros->sum('valor_maximo');
    }

    public function sumaEsCien(): bool
    {
        return $this->sumaValorMaximo() === 100;
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'id_categoria');
    }

    public function requiereNombrePresentacion(): bool
    {
        return preg_match('/d[uú]o|tr[ií]o|estudiantina|especial/i', $this->categoria) === 1;
    }
}
