<?php

namespace App\Modules\Usuarios\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InicioController extends Controller
{
    public function admin(): View
    {
        return view('admin.inicio');
    }

    public function jurado(Request $request): View
    {
        return view('jurado.inicio');
    }

    public function publico(): View
    {
        return view('publico.inicio');
    }
}