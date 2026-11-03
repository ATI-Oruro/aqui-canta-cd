<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#050204]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Aquí… Canta Bolivia') }} — Iniciar Sesión</title>

        <link rel="icon" type="image/png" href="/images/logo-oficial.png">
        <link rel="shortcut icon" href="/images/favicon.ico">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <style>
            .font-cinzel { font-family: 'Cinzel', serif !important; }
            .font-outfit { font-family: 'Outfit', sans-serif !important; }

            .gold-gradient-text {
                background: linear-gradient(135deg, #FFF8DC 0%, #F5D061 40%, #D4AF37 75%, #AA7C11 100%) !important;
                -webkit-background-clip: text !important;
                -webkit-text-fill-color: transparent !important;
            }

            .corner-bracket {
                position: absolute;
                width: 24px;
                height: 24px;
                border-color: #d4af37 !important;
                border-style: solid !important;
                pointer-events: none;
                z-index: 25;
                transition: border-color 0.3s ease, filter 0.3s ease !important;
            }
            .corner-tl { top: 20px; left: 20px; border-width: 2px 0 0 2px !important; }
            .corner-tr { top: 20px; right: 20px; border-width: 2px 2px 0 0 !important; }
            .corner-bl { bottom: 20px; left: 20px; border-width: 0 0 2px 2px !important; }
            .corner-br { bottom: 20px; right: 20px; border-width: 0 2px 2px 0 !important; }

            .card-dot-pattern {
                background-image: radial-gradient(rgba(212, 175, 55, 0.14) 1px, transparent 1px);
                background-size: 18px 18px;
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-outfit antialiased min-h-screen w-full bg-[#050204] text-slate-100 selection:bg-[#800020] selection:text-amber-200 overflow-x-hidden">
        <main class="min-h-screen w-full flex">
            {{ $slot }}
        </main>
    </body>
</html>
