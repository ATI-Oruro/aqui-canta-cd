<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ACB Canta Bolivia') }} — Público</title>

        <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
    </head>
    <body class="font-sans antialiased min-h-screen bg-cover bg-center bg-fixed" style="background-image: url('/images/fondo.png');">
        <div class="min-h-screen bg-black/50 backdrop-blur-[2px]">
            <header class="bg-white/95 shadow-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="/images/acb_logo.png" alt="ACB" class="h-12 w-12 object-contain">
                        <div>
                            <p class="font-extrabold text-slate-900 leading-tight text-lg">ACB Canta Bolivia</p>
                            <p class="text-xs text-slate-500">Ranking en vivo</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @auth
                            <span class="text-sm text-slate-600 hidden sm:block">{{ auth()->user()->nombre }}</span>
                            @if (auth()->user()->esAdmin())
                                <a href="{{ route('admin.inicio') }}" class="text-sm font-medium text-red-700 hover:underline">Panel</a>
                            @else
                                <a href="{{ route('jurado.inicio') }}" class="text-sm font-medium text-red-700 hover:underline">Mi panel</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-900">Salir</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Ingresar</a>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                {{ $slot }}
            </main>

            <footer class="mt-16 border-t border-white/20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-sm text-white/80">
                    XXXIII Festival Nacional de la Canción Boliviana
                    “Aquí… Canta Bolivia” — Oruro, 20 y 21 de noviembre de 2026
                </div>
            </footer>
        </div>
    </body>
</html>