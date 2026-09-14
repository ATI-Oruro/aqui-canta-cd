<?php

namespace Tests\Feature\Jurados;

use App\Modules\Categorias\Models\Categoria;
use App\Modules\Categorias\Models\CategoriaParametro;
use App\Modules\Departamentos\Models\Departamento;
use App\Modules\Fases\Models\Fase;
use App\Modules\Inscripciones\Models\Inscripcion;
use App\Modules\Parametros\Models\Parametro;
use App\Modules\Participantes\Models\Participante;
use App\Modules\Presentaciones\Models\Presentacion;
use App\Modules\Usuarios\Models\Usuario;
use App\Support\Enums\FaseEnum;
use App\Support\Enums\PresentacionEstadoEnum;
use App\Support\Enums\RolEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminJuradoSecretoTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $jurado;
    private Categoria $categoria;
    private Presentacion $presentacion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Usuario::create([
            'username' => 'admin_test',
            'nombre' => 'Admin',
            'paterno' => 'Sistema',
            'ci' => '111222',
            'email' => 'admin@test.com',
            'password' => 'password',
            'rol' => RolEnum::ADMIN,
        ]);

        $this->jurado = Usuario::create([
            'username' => 'jurado_test',
            'nombre' => 'Jurado',
            'paterno' => 'Evaluador',
            'ci' => '333444',
            'email' => 'jurado@test.com',
            'password' => 'password',
            'rol' => RolEnum::JURADO,
        ]);

        $this->categoria = Categoria::create([
            'categoria' => 'Estudiantina',
            'max_participantes' => 10,
            'tiempo_presentacion' => 8,
        ]);

        $param = Parametro::create(['parametro' => 'Interpretación']);
        CategoriaParametro::create([
            'id_categoria' => $this->categoria->id,
            'id_parametro' => $param->id,
            'valor_minimo' => 1,
            'valor_maximo' => 100,
            'orden' => 1,
        ]);

        $fase = Fase::create(['fase' => FaseEnum::FINAL->value]);
        $departamento = Departamento::create(['nombre' => 'La Paz']);
        $part = Participante::create(['nombre' => 'Estudiantina', 'paterno' => 'Paceña']);

        $inscripcion = Inscripcion::create([
            'fecha_inscripcion' => now(),
            'id_fase' => $fase->id,
            'id_categoria' => $this->categoria->id,
            'id_departamento' => $departamento->id,
            'estado' => \App\Support\Enums\InscripcionEstadoEnum::CONFIRMADA,
        ]);
        $inscripcion->participantes()->attach($part->id);

        $this->presentacion = Presentacion::create([
            'id_inscripcion' => $inscripcion->id,
            'nombre_presentacion' => 'Estudiantina Paceña',
            'estado' => PresentacionEstadoEnum::POR_PARTICIPAR,
            'tiempo_maximo' => 8,
        ]);
    }

    public function test_admin_puede_ver_panel_de_jurado_secreto(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.jurados.secreto'));

        $response->assertOk();
        $response->assertSee('Jurado Secreto');
        $response->assertSee('Estudiantina');
    }

    public function test_jurado_no_puede_acceder_al_panel_de_jurado_secreto(): void
    {
        $response = $this->actingAs($this->jurado)->get(route('admin.jurados.secreto'));

        $response->assertForbidden();
    }

    public function test_admin_puede_designar_jurado_secreto(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.jurados.secreto.designar'), [
            'categoria_id' => $this->categoria->id,
            'usuario_id' => $this->jurado->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    public function test_admin_puede_revelar_y_ocultar_jurado_secreto(): void
    {
        $this->assertFalse($this->categoria->estaRevelada());

        // Revelar
        $responseRevelar = $this->actingAs($this->admin)
            ->post(route('admin.categorias.revelar', $this->categoria));

        $responseRevelar->assertRedirect();
        $this->assertTrue($this->categoria->fresh()->estaRevelada());

        // Ocultar
        $responseOcultar = $this->actingAs($this->admin)
            ->post(route('admin.categorias.ocultar', $this->categoria));

        $responseOcultar->assertRedirect();
        $this->assertFalse($this->categoria->fresh()->estaRevelada());
    }

    public function test_admin_puede_finalizar_y_reabrir_categoria(): void
    {
        $this->assertFalse($this->categoria->estaFinalizada());

        // Finalizar
        $responseFinalizar = $this->actingAs($this->admin)
            ->post(route('admin.categorias.finalizar', $this->categoria));

        $responseFinalizar->assertRedirect();
        $this->assertTrue($this->categoria->fresh()->estaFinalizada());
        $this->assertTrue($this->categoria->fresh()->estaRevelada());

        // Reabrir
        $responseReabrir = $this->actingAs($this->admin)
            ->post(route('admin.categorias.reabrir', $this->categoria));

        $responseReabrir->assertRedirect();
        $this->assertFalse($this->categoria->fresh()->estaFinalizada());
    }
}
