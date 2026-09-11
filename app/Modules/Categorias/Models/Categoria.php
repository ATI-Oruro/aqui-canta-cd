<?php

namespace App\Modules\Categorias\Models;

use Illuminate\Database\Eloquent\Model;
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

    public function parametros(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
}
