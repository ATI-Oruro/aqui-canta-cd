<?php

use App\Modules\Categorias\Controllers\ConfiguracionController;
use App\Modules\Importaciones\Controllers\ImportacionController;
use App\Modules\Inscripciones\Controllers\InscripcionController;
use App\Modules\Participantes\Controllers\ParticipanteController;
use App\Modules\Usuarios\Controllers\DashboardController;
use App\Modules\Usuarios\Controllers\InicioController;
use App\Modules\Usuarios\Controllers\UsuariosController;
use App\Support\Enums\RolEnum;
use Illuminate\Support\Facades\Route;

// Página pública
Route::get('/', [InicioController::class, 'publico'])->name('inicio');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Panel de administración
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', sprintf('rol:%s', RolEnum::ADMIN->value)])->group(function () {
    Route::get('/inicio', [InicioController::class, 'admin'])->name('inicio');
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion');

    Route::resource('usuarios', UsuariosController::class)
        ->except(['show'])
        ->names('usuarios');

    Route::resource('participantes', ParticipanteController::class)
        ->except(['show'])
        ->names('participantes');

    Route::resource('inscripciones', InscripcionController::class)
        ->parameters(['inscripciones' => 'inscripcion'])
        ->except(['show'])
        ->names('inscripciones');

    Route::prefix('importaciones')->name('importaciones.')->group(function () {
        Route::get('/', [ImportacionController::class, 'index'])->name('index');
        Route::post('/importar', [ImportacionController::class, 'importar'])->name('importar');
        Route::get('/plantilla', [ImportacionController::class, 'plantilla'])->name('plantilla');
    });
});

// Panel de jurado
Route::prefix('jurado')->name('jurado.')->middleware(['auth', 'verified', sprintf('rol:%s', RolEnum::JURADO->value)])->group(function () {
    Route::get('/inicio', [InicioController::class, 'jurado'])->name('inicio');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';