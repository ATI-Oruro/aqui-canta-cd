<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#050204]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Aquí Canta Bolivia') }} — XXXIII Festival Nacional</title>

        <link rel="icon" type="image/png" href="/images/logo-oficial.png">
        <link rel="shortcut icon" href="/images/favicon.ico">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <style>
            .font-cinzel { font-family: 'Cinzel', serif; }
            .font-outfit { font-family: 'Outfit', sans-serif; }
            
            /* Esquineros dorados de gala */
            .corner-bracket-tl { position: absolute; top: 8px; left: 8px; width: 14px; height: 14px; border-top: 1.5px solid #d4af37; border-left: 1.5px solid #d4af37; pointer-events: none; z-index: 10; }
            .corner-bracket-tr { position: absolute; top: 8px; right: 8px; width: 14px; height: 14px; border-top: 1.5px solid #d4af37; border-right: 1.5px solid #d4af37; pointer-events: none; z-index: 10; }
            .corner-bracket-bl { position: absolute; bottom: 8px; left: 8px; width: 14px; height: 14px; border-bottom: 1.5px solid #d4af37; border-left: 1.5px solid #d4af37; pointer-events: none; z-index: 10; }
            .corner-bracket-br { position: absolute; bottom: 8px; right: 8px; width: 14px; height: 14px; border-bottom: 1.5px solid #d4af37; border-right: 1.5px solid #d4af37; pointer-events: none; z-index: 10; }
            
            /* Gradientes de oro y carmesí */
            .gold-gradient-text {
                background: linear-gradient(135deg, #fffbeb 0%, #fef08a 25%, #d4af37 60%, #b45309 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .oruro-gradient-text {
                background: linear-gradient(135deg, #fef08a 0%, #d4af37 50%, #e11d48 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            /* Scrollbars guindo sobrio estilo Oruro */
            ::-webkit-scrollbar {
                width: 5px;
                height: 5px;
            }
            ::-webkit-scrollbar-track {
                background: transparent;
            }
            ::-webkit-scrollbar-thumb {
                background: #6b0015;
                border-radius: 9999px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #990024;
            }
            * {
                scrollbar-width: thin;
                scrollbar-color: #6b0015 transparent;
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
    </head>
    <body class="font-outfit antialiased min-h-screen bg-[#050204] text-slate-100 selection:bg-[#800020] selection:text-amber-200 flex flex-col justify-between overflow-x-hidden">
        
        <!-- Fondo Oruro Gala: Carmesí profundo, Guindo y Negro Obsidiana -->
        <div class="fixed inset-0 pointer-events-none z-0 bg-[radial-gradient(ellipse_100%_80%_at_50%_-15%,rgba(128,0,32,0.45),rgba(5,2,4,0.98))]"></div>

        <div class="relative z-10 flex flex-col min-h-screen justify-between">
            
            <!-- Barra Superior Principal de Gala -->
            <header class="border-b border-[#6b0015]/40 bg-[#070205]/95 backdrop-blur-xl sticky top-0 z-40 shadow-2xl">
                <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-10 py-3.5 flex items-center justify-between gap-4">
                    
                    <!-- Identidad / Logo del Festival (Sin recuadro dorado) -->
                    <div class="flex items-center gap-3.5 cursor-pointer" onclick="window.location.href='{{ route('inicio') }}'">
                        <img src="/images/logo-oficial.png" alt="Aquí Canta Bolivia" class="h-10 w-10 object-contain drop-shadow" onerror="this.src='/images/logo-oficial.png'">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-cinzel text-base sm:text-lg font-black tracking-[0.15em] gold-gradient-text uppercase">
                                    Aquí… Canta Bolivia
                                </span>
                            </div>
                            <p class="text-[10px] sm:text-[11px] font-semibold tracking-[0.2em] text-[#d4af37]/80 uppercase">
                                XXXIII Festival Nacional · Oruro 2026
                            </p>
                        </div>
                    </div>

                    <!-- Estado del Festival en Vivo -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#120409]/90 border border-[#6b0015]/60 text-[11px] text-slate-300 shadow-inner">
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            <span class="font-semibold tracking-wider uppercase text-[10px] text-slate-400">Transmisión Oficial</span>
                            <span class="text-[#6b0015]">|</span>
                            <span class="font-bold text-amber-300 font-cinzel">Teatro Oruro</span>
                        </div>
                        @auth
                            <a href="{{ auth()->user()->esAdmin() ? route('admin.inicio') : route('jurado.inicio') }}"
                               class="text-[11px] font-bold text-amber-200/80 hover:text-white bg-[#1a050d] px-3 py-1 rounded-lg border border-[#6b0015] hover:border-amber-400/50 transition">
                                Panel
                            </a>
                        @endauth
                    </div>

                </div>
            </header>

            <!-- Contenido Principal -->
            <main class="flex-1 max-w-[1700px] w-full mx-auto px-3 sm:px-6 lg:px-10 py-6">
                {{ $slot }}
            </main>

            <!-- Pie de Página de Gala -->
            <footer class="mt-12 border-t border-[#3b000b] bg-[#050204]/90 backdrop-blur-md">
                <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-10 py-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-400">
                    <div class="flex items-center gap-3">
                        <span class="font-cinzel font-bold text-[#d4af37] uppercase tracking-widest text-xs">Aquí Canta Bolivia</span>
                        <span class="text-[#6b0015]">·</span>
                        <span>Gobierno Autónomo Departamental de Oruro</span>
                    </div>

                    <div class="text-center md:text-right font-medium text-slate-400">
                        XXXIII Festival Nacional de la Canción Boliviana · 20 y 21 de noviembre de 2026
                    </div>
                </div>
            </footer>

        </div>
    </body>
</html>