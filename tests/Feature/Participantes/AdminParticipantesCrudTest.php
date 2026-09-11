<?php

namespace Tests\Feature\Participantes;

use App\Modules\Inscripciones\Services\RegistrarInscripcionService;
use App\Modules\Participantes\Models\Participante;
use App\Modules\Usuarios\Models\Usuario;
use App\Support\Enums\RolEnum;
use Database\Seeders\CategoriaParametrosSeeder;
use Database\Seeders\DepartamentoSeeder;
use Database\Seeders\FaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminParticipantesCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            DepartamentoSeeder::class,
            FaseSeeder::class,
            CategoriaParametrosSeeder::class,
        ]);
    }

    private function admin(): Usuario
    {
        return Usuario::factory()->administrador()->create();
    }

    private function datosInscripcion(): array
    {
        return [
            'categoria' => 'Ñusta',
            'departamento' => 'Oruro',
            'fase' => 'PRESELECCION',
            'fecha_inscripcion' => '2026-09-11',
            'fecha_presentacion' => null,
            'hora_presentacion' => null,
            'nombre_presentacion' => null,
            'integrantes' => [
                ['nombre' => 'Ana', 'paterno' => 'García', 'materno' => null, 'pseudonimo' => null],
            ],
        ];
    }

    #[Test]
    public function un_jurado_no_puede_gestionar_participantes(): void
    {
        $jurado = Usuario::factory()->create(['rol' => RolEnum::JURADO]);

        $this->actingAs($jurado)
            ->get(route('admin.participantes.index'))
            ->assertForbidden();
    }

    #[Test]
    public function un_admin_puede_listar_y_buscar_participantes(): void
    {
        Participante::create(['nombre' => 'Camila', 'paterno' => 'Mamani', 'materno' => 'Rivera']);

        $this->actingAs($this->admin())
            ->get(route('admin.participantes.index'))
            ->assertOk()
            ->assertSee('Camila');

        $this->actingAs($this->admin())
            ->get(route('admin.participantes.index', ['busqueda' => 'mamani']))
            ->assertSee('Camila');

        $this->actingAs($this->admin())
            ->get(route('admin.participantes.index', ['busqueda' => 'inexistente']))
            ->assertDontSee('Camila')
            ->assertSee('Sin participantes registrados.');
    }

    #[Test]
    public function un_admin_puede_registrar_un_participante(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.participantes.store'), [
                'nombre' => 'Lucía',
                'paterno' => 'Rojas',
                'materno' => 'Cruz',
                'pseudonimo' => 'Lucía Andina',
            ])
            ->assertRedirect(route('admin.participantes.index'));

        $this->assertDatabaseHas('participantes', [
            'nombre' => 'Lucía',
            'paterno' => 'Rojas',
            'materno' => 'Cruz',
            'pseudonimo' => 'Lucía Andina',
        ]);
    }

    #[Test]
    public function no_permite_registrar_un_participante_duplicado(): void
    {
        $admin = $this->admin();
        $datos = ['nombre' => 'Ana', 'paterno' => 'García'];

        $this->actingAs($admin)->post(route('admin.participantes.store'), $datos)->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.participantes.store'), $datos)
            ->assertSessionHasErrors('nombre');

        $this->assertSame(1, Participante::where('nombre', 'Ana')->count());
    }

    #[Test]
    public function un_admin_puede_editar_un_participante(): void
    {
        $participante = Participante::create(['nombre' => 'Ana', 'paterno' => 'García']);

        $this->actingAs($this->admin())
            ->patch(route('admin.participantes.update', $participante), [
                'nombre' => 'Ana',
                'paterno' => 'García',
                'materno' => 'López',
                'pseudonimo' => 'Ana del Sur',
            ])
            ->assertRedirect(route('admin.participantes.index'));

        $this->assertDatabaseHas('participantes', [
            'id' => $participante->id,
            'materno' => 'López',
            'pseudonimo' => 'Ana del Sur',
        ]);
    }

    #[Test]
    public function editar_sin_cambiar_datos_no_considera_duplicado(): void
    {
        $participante = Participante::create(['nombre' => 'Ana', 'paterno' => 'García']);

        $this->actingAs($this->admin())
            ->patch(route('admin.participantes.update', $participante), [
                'nombre' => 'Ana',
                'paterno' => 'García',
            ])
            ->assertRedirect(route('admin.participantes.index'));
    }

    #[Test]
    public function no_permite_editar_a_otro_participante_con_los_mismos_datos(): void
    {
        Participante::create(['nombre' => 'Ana', 'paterno' => 'García', 'materno' => 'Cruz']);
        $otro = Participante::create(['nombre' => 'Beatriz', 'paterno' => 'García']);

        $this->actingAs($this->admin())
            ->patch(route('admin.participantes.update', $otro), [
                'nombre' => 'Ana',
                'paterno' => 'García',
                'materno' => 'Cruz',
            ])
            ->assertSessionHasErrors('nombre');
    }

    #[Test]
    public function un_admin_puede_eliminar_un_participante_sin_inscripciones(): void
    {
        $participante = Participante::create(['nombre' => 'Ana']);

        $this->actingAs($this->admin())
            ->delete(route('admin.participantes.destroy', $participante))
            ->assertRedirect(route('admin.participantes.index'));

        $this->assertDatabaseMissing('participantes', ['id' => $participante->id]);
    }

    #[Test]
    public function bloquea_eliminar_un_participante_vinculado_a_inscripciones(): void
    {
        app(RegistrarInscripcionService::class)->registrar($this->datosInscripcion());

        $participante = Participante::where('nombre', 'Ana')->firstOrFail();

        $this->actingAs($this->admin())
            ->delete(route('admin.participantes.destroy', $participante))
            ->assertForbidden();

        $this->assertDatabaseHas('participantes', ['id' => $participante->id]);
    }
}
