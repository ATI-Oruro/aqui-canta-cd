<?php

namespace Tests\Feature\Presentaciones;

use App\Modules\Usuarios\Models\Usuario;
use App\Support\Enums\PresentacionEstadoEnum;
use App\Support\Enums\RolEnum;
use Database\Seeders\CategoriaParametrosSeeder;
use Database\Seeders\DepartamentoSeeder;
use Database\Seeders\FaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\CreaPresentacion;
use Tests\TestCase;

class EscenarioVistaTest extends TestCase
{
    use CreaPresentacion, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            DepartamentoSeeder::class,
            FaseSeeder::class,
            CategoriaParametrosSeeder::class,
        ]);
    }

    #[Test]
    public function un_admin_puede_ver_el_escenario(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $presentacion = $this->presentacionPreseleccion();
        $presentacion->forceFill(['estado' => PresentacionEstadoEnum::EN_ESCENARIO])->save();

        $this->actingAs($admin)
            ->get(route('admin.presentaciones.escenario'))
            ->assertOk()
            ->assertSee('Presentación de prueba')
            ->assertSee('En escenario');
    }

    #[Test]
    public function un_jurado_puede_ver_la_presentacion_actual(): void
    {
        $jurado = Usuario::factory()->create(['rol' => RolEnum::JURADO]);
        $presentacion = $this->presentacionPreseleccion();
        $presentacion->forceFill(['estado' => PresentacionEstadoEnum::PARTICIPANDO])->save();

        $this->actingAs($jurado)
            ->get(route('jurado.presentaciones.actual'))
            ->assertOk()
            ->assertSee('Presentación de prueba')
            ->assertSee('Participando');
    }

    #[Test]
    public function el_escenario_muestra_la_siguiente_presentacion_cuando_no_hay_una_en_curso(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $this->presentacionPreseleccion();

        $this->actingAs($admin)
            ->get(route('admin.presentaciones.escenario'))
            ->assertOk()
            ->assertSee('Siguiente');
    }

    #[Test]
    public function el_escenario_requiere_autenticacion(): void
    {
        $this->get(route('admin.presentaciones.escenario'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function un_visitante_no_autenticado_no_ve_el_escenario_del_jurado(): void
    {
        $this->get(route('jurado.presentaciones.actual'))
            ->assertRedirect(route('login'));
    }
}