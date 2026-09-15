<?php

namespace App\Modules\Categorias\Models;

use App\Modules\Inscripciones\Models\Inscripcion;
use App\Modules\Parametros\Models\Parametro;
use App\Modules\Usuarios\Models\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $fillable = [
        'categoria',
        'max_participantes',
        'tiempo_presentacion',
        'jurado_secreto_usuario_id',
        'jurado_secreto_revelado',
        'revelado_at',
        'finalizada',
        'finalizada_at',
        'es_activa',
    ];

    protected $casts = [
        'max_participantes' => 'integer',
        'tiempo_presentacion' => 'integer',
        'jurado_secreto_usuario_id' => 'integer',
        'jurado_secreto_revelado' => 'boolean',
        'revelado_at' => 'datetime',
        'finalizada' => 'boolean',
        'finalizada_at' => 'datetime',
        'es_activa' => 'boolean',
    ];

    public function juradoSecreto(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'jurado_secreto_usuario_id');
    }

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

    public function esNusta(): bool
    {
        return mb_stripos($this->categoria, 'ñusta') !== false || mb_stripos($this->categoria, 'nusta') !== false;
    }

    public function tiempoParaFase(\App\Support\Enums\FaseEnum|string|null $fase = null): int
    {
        $faseStr = $fase instanceof \App\Support\Enums\FaseEnum ? $fase->value : (string) $fase;
        if ($this->esNusta() && strtoupper($faseStr) === \App\Support\Enums\FaseEnum::FINAL->value) {
            return 15;
        }

        return (int) $this->tiempo_presentacion;
    }

    public function estaFinalizada(): bool
    {
        return (bool) $this->finalizada;
    }

    public function estaRevelada(): bool
    {
        return (bool) $this->jurado_secreto_revelado || $this->estaFinalizada();
    }

    public function marcarRevelada(): void
    {
        $this->update([
            'jurado_secreto_revelado' => true,
            'revelado_at' => now(),
        ]);
    }

    public function marcarOculta(): void
    {
        $this->update([
            'jurado_secreto_revelado' => false,
            'revelado_at' => null,
        ]);
    }

    public function marcarFinalizada(): void
    {
        $this->update([
            'finalizada' => true,
            'finalizada_at' => now(),
            'jurado_secreto_revelado' => true,
            'revelado_at' => $this->revelado_at ?? now(),
        ]);
    }

    public function marcarReabierta(): void
    {
        $this->update([
            'finalizada' => false,
            'finalizada_at' => null,
        ]);
    }

    public function marcarActiva(): void
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn('categorias', 'es_activa')) {
            self::where('es_activa', true)->where('id', '!=', $this->id)->update(['es_activa' => false]);
            $this->update(['es_activa' => true]);
        }
    }

    public static function categoriaActiva(?int $categoriaId = null): ?Categoria
    {
        if ($categoriaId !== null && $categoriaId > 0) {
            $cat = self::find($categoriaId);
            if ($cat) {
                return $cat;
            }
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('categorias', 'es_activa')) {
            $activa = self::where('es_activa', true)->first();
            if ($activa) {
                return $activa;
            }
        }

        $presEnEscenario = \App\Modules\Presentaciones\Models\Presentacion::query()
            ->whereIn('estado', [
                \App\Support\Enums\PresentacionEstadoEnum::EN_ESCENARIO->value,
                \App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO->value,
            ])
            ->with('inscripcion.categoria')
            ->latest('updated_at')
            ->first();

        if ($presEnEscenario?->inscripcion?->categoria) {
            return $presEnEscenario->inscripcion->categoria;
        }

        return self::whereHas('inscripciones.fase', fn ($f) => $f->where('fase', \App\Support\Enums\FaseEnum::FINAL->value))
            ->first() ?? self::orderBy('id')->first();
    }
}

