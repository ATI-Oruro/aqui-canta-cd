<?php

namespace Tests\Feature\Rankings;

use App\Modules\Calificaciones\Models\Calificacion;
use App\Modules\Calificaciones\Models\DetalleCalificacion;
use App\Modules\Categorias\Models\Categoria;
use App\Modules\Categorias\Models\CategoriaParametro;
use App\Modules\Departamentos\Models\Departamento;
use App\Modules\Fases\Models\Fase;
use App\Modules\Inscripciones\Models\Inscripcion;
use App\Modules\Jurados\Services\AsignarJuradosService;
use App\Modules\Jurados\Services\GestionarJuradoSecretoService;
use App\Modules\Parametros\Models\Parametro;
use App\Modules\Participantes\Models\Participante;
use App\Modules\Presentaciones\Models\Presentacion;
use App\Modules\Usuarios\Models\Usuario;
use App\Support\Enums\CalificacionEstadoEnum;
use App\Support\Enums\FaseEnum;
use App\Support\Enums\InscripcionEstadoEnum;
use App\Support\Enums\PresentacionEstadoEnum;
use App\Support\Enums\RolEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicoRankingTest extends TestCase
{
    use RefreshDatabase;

    private function setupEscenarioPublico(): array
    {
        $categoria1 = Categoria::create([
            'categoria' => 'Solista vocal',
            'max_participantes' => 1,
            'tiempo_presentacion' => 10,
        ]);

        $categoria2 = Categoria::create([
            'categoria' => 'Dúo vocal instrumental',
            'max_participantes' => 2,
            'tiempo_presentacion' => 10,
        ]);

        $parametro = Parametro::create(['parametro' => 'Interpretación']);
        CategoriaParametro::create([
            'id_categoria' => $categoria1->id,
            'id_parametro' => $parametro->id,
            'valor_minimo' => 1,
            'valor_maximo' => 100,
            'orden' => 1,
        ]);

        CategoriaParametro::create([
            'id_categoria' => $categoria2->id,
            'id_parametro' => $parametro->id,
            'valor_minimo' => 1,
            'valor_maximo' => 100,
            'orden' => 1,
        ]);

        $fase = Fase::create(['fase' => FaseEnum::FINAL->value]);
        $oruro = Departamento::create(['nombre' => 'Oruro']);
        $laPaz = Departamento::create(['nombre' => 'La Paz']);

        // Presentación 1 (Oruro, Solista)
        $part1 = Participante::create(['nombre' => 'Mateo', 'paterno' => 'Flores']);
        $insc1 = Inscripcion::create([
            'fecha_inscripcion' => now(),
            'id_fase' => $fase->id,
            'id_categoria' => $categoria1->id,
            'id_departamento' => $oruro->id,
            'estado' => InscripcionEstadoEnum::CONFIRMADA,
        ]);
        $insc1->participantes()->attach($part1->id);
        $pres1 = Presentacion::create([
            'id_inscripcion' => $insc1->id,
            'nombre_presentacion' => 'Mateo Flores',
            'estado' => PresentacionEstadoEnum::FINALIZADO,
            'tiempo_maximo' => 10,
            'nota_final' => 96.5,
        ]);

        // Presentación 2 (La Paz, Dúo)
        $part2 = Participante::create(['nombre' => 'Valeria', 'paterno' => 'Quiroga']);
        $insc2 = Inscripcion::create([
            'fecha_inscripcion' => now(),
            'id_fase' => $fase->id,
            'id_categoria' => $categoria2->id,
            'id_departamento' => $laPaz->id,
            'estado' => InscripcionEstadoEnum::CONFIRMADA,
        ]);
        $insc2->participantes()->attach($part2->id);
        $pres2 = Presentacion::create([
            'id_inscripcion' => $insc2->id,
            'nombre_presentacion' => 'Dúo Armonía',
            'estado' => PresentacionEstadoEnum::FINALIZADO,
            'tiempo_maximo' => 10,
            'nota_final' => 91.0,
        ]);

        // Jurados
        $jurados = [];
        for ($i = 1; $i <= 5; $i++) {
            $jurados[] = Usuario::create([
                'username' => "jurado{$i}_".uniqid(),
                'nombre' => "Maestro{$i}",
                'paterno' => "Paterno{$i}",
                'ci' => "888{$i}".rand(10, 99),
                'email' => "m{$i}_".uniqid().'@test.com',
                'password' => 'password',
                'rol' => RolEnum::JURADO,
            ]);
        }

        $asignarService = app(AsignarJuradosService::class);
        $asignarService->asignar($pres1, array_map(fn ($j) => $j->id, $jurados), $jurados[4]->id);

        foreach ($pres1->jurados as $jp) {
            $cal = Calificacion::create([
                'id_jurado_presentacion' => $jp->id,
                'nota_total' => 96.5,
                'estado' => CalificacionEstadoEnum::EMITIDO,
                'fecha_finalizacion' => now(),
            ]);
            DetalleCalificacion::create([
                'id_calificacion' => $cal->id,
                'id_parametro' => $parametro->id,
                'valor' => 96.5,
            ]);
        }

        return compact('categoria1', 'categoria2', 'oruro', 'laPaz', 'pres1', 'pres2', 'jurados');
    }

    public function test_vista_publica_carga_correctamente_sin_boton_de_ingreso(): void
    {
        $response = $this->get(route('inicio'));

        $response->assertStatus(200);
        $response->assertSee('Aquí… Canta Bolivia');
        $response->assertSee('XXXIII Festival Nacional');
        
        // Regla crítica: el botón de ingreso NO debe ser visible
        $response->assertDontSee('Ingresar');
        $response->assertDontSee('/login');
    }

    public function test_vista_publica_muestra_participaciones_y_calificaciones(): void
    {
        $data = $this->setupEscenarioPublico();

        $response = $this->get(route('inicio'));

        $response->assertStatus(200);
        $response->assertSee('Mateo Flores');
        $response->assertSee('Dúo Armonía');
        $response->assertSee('96.50');
        $response->assertSee('91.00');
    }

    public function test_vista_publica_filtra_por_departamento(): void
    {
        $data = $this->setupEscenarioPublico();

        $response = $this->get(route('inicio', ['departamento' => $data['oruro']->id]));

        $response->assertStatus(200);
        $response->assertSee('Mateo Flores');
        $response->assertDontSee('Dúo Armonía');
    }

    public function test_vista_publica_filtra_por_categoria(): void
    {
        $data = $this->setupEscenarioPublico();

        $response = $this->get(route('inicio', ['categoria' => $data['categoria2']->id]));

        $response->assertStatus(200);
        $response->assertSee('Dúo Armonía');
        $response->assertDontSee('Mateo Flores');
    }

    public function test_vista_publica_enmascara_calificacion_reservada_cuando_no_esta_revelada(): void
    {
        $data = $this->setupEscenarioPublico();
        $categoria1 = $data['categoria1'];

        $this->assertFalse($categoria1->estaRevelada());

        $response = $this->get(route('inicio', ['categoria' => $categoria1->id]));

        $response->assertStatus(200);
        // Debe mostrar "Calificación Reservada" o "En Resguardo" y no el nombre del jurado secreto
        $response->assertSee('Calificación Reservada');
        $juradoSecreto = $data['jurados'][4];
        $response->assertDontSee("{$juradoSecreto->nombre} {$juradoSecreto->paterno}");
    }

    public function test_vista_publica_muestra_puntaje_oficial_al_revelar(): void
    {
        $data = $this->setupEscenarioPublico();
        $categoria1 = $data['categoria1'];
        $juradoSecreto = $data['jurados'][4];

        app(GestionarJuradoSecretoService::class)->revelarJuradoSecreto($categoria1);

        $response = $this->get(route('inicio', ['categoria' => $categoria1->id]));

        $response->assertStatus(200);
        $response->assertSee('Puntaje Oficial Consolidado');
        $response->assertSee("{$juradoSecreto->nombre} {$juradoSecreto->paterno}");
    }

    public function test_vista_publica_modo_tabla_y_sin_pestanas_innecesarias(): void
    {
        $data = $this->setupEscenarioPublico();

        $response = $this->get(route('inicio', ['vista' => 'tabla']));
        $response->assertStatus(200);
        $response->assertSee('Participación Oficial');
        $response->assertSee('Mesa de Evaluación');
        
        // El nav no debe tener las opciones removidas
        $response->assertDontSee('En Escenario');
        $response->assertDontSee('Directorio Oficial');
    }
}
