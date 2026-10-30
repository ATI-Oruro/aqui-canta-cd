<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Aquí Canta Bolivia') }} — Administración</title>

        <link rel="icon" type="image/png" href="/images/logo-oficial.png">
        <link rel="shortcut icon" href="/images/favicon.ico">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
    </head>
    <body class="font-sans antialiased min-h-screen bg-slate-100">
        <div class="flex min-h-screen">
            <!-- Sidebar fijo y estático en tamaño (100vh) con color vino tinto oscuro -->
            <aside style="background: linear-gradient(180deg, #36040e 0%, #240209 100%);" class="fixed inset-y-0 left-0 z-30 w-64 h-screen shrink-0 bg-[#2d030c] text-white flex flex-col shadow-xl select-none print:hidden">
                <div class="px-6 py-4 flex items-center gap-3 border-b border-white/10 shrink-0">
                    <img src="/images/logo-oficial.png" alt="ACB" class="h-10 w-10 object-contain drop-shadow">
                    <div>
                        <p class="font-bold text-white leading-tight">Aquí Canta Bolivia</p>
                        <p class="text-xs text-rose-200/70">Administración</p>
                    </div>
                </div>

                <nav class="flex-1 px-3 py-3 space-y-1.5 text-sm overflow-hidden">
                    {{ $sidebar }}
                </nav>

                <div class="px-6 py-4 border-t border-white/10 bg-black/20 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-white/10 ring-1 ring-white/20 flex items-center justify-center uppercase text-white font-bold shrink-0">
                            {{ substr(auth()->user()->nombre ?? 'U', 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-white text-sm">{{ auth()->user()->nombre }} {{ auth()->user()->paterno }}</p>
                            <p class="text-xs text-rose-200/70">Admin</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2.5">
                        @csrf
                        <button type="submit" class="w-full text-left text-xs text-rose-200/70 hover:text-white py-1 transition-colors">
                            Cerrar sesión →
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main -->
            <div class="flex-1 flex flex-col min-w-0 ml-64 print:ml-0">
                @isset($header)
                    <header class="bg-white shadow-sm border-b border-slate-200">
                        <div class="px-8 py-5">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="flex-1 p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
