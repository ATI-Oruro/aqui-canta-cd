@php
    $enFestival = request()->routeIs('admin.configuracion', 'admin.usuarios.*');
    $enOperacion = request()->routeIs(
        'admin.importaciones.*',
        'admin.participantes.*',
        'admin.inscripciones.*',
        'admin.presentaciones.*',
        'admin.jurados.*'
    );
    $enResultados = request()->routeIs('admin.reportes.*', 'admin.auditoria.*');

    // Si está en Inicio (ninguno activo), dejamos abierto Festival por defecto
    if (! $enFestival && ! $enOperacion && ! $enResultados) {
        $enFestival = true;
    }
@endphp

<a href="{{ route('admin.inicio') }}" @class([
    'flex items-center gap-2.5 px-4 py-2.5 rounded-lg transition-colors',
    'bg-white/15 text-white font-semibold shadow-sm ring-1 ring-white/20' => request()->routeIs('admin.inicio'),
    'text-rose-100/90 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.inicio'),
])>
    <span>Inicio</span>
</a>

<!-- Grupo: Festival -->
<div class="sidebar-group pt-1" data-group="festival">
    <button type="button" onclick="toggleSidebarGroup('festival')"
        class="w-full flex items-center justify-between px-4 py-2 rounded-lg text-xs font-semibold uppercase tracking-wider text-rose-200/80 hover:bg-white/10 hover:text-white transition-colors">
        <span>Festival</span>
        <svg data-chevron="festival" class="w-4 h-4 transition-transform duration-200 {{ $enFestival ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <div id="sidebar-group-festival" class="mt-1 space-y-1 pl-2 border-l border-white/15 ml-4 {{ $enFestival ? '' : 'hidden' }}">
        <a href="{{ route('admin.configuracion') }}" @class([
            'block px-3 py-1.5 rounded-lg transition-colors text-sm',
            'bg-white/15 text-white font-semibold shadow-sm ring-1 ring-white/20' => request()->routeIs('admin.configuracion'),
            'text-rose-100/85 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.configuracion'),
        ])>
            Configuración
        </a>
        <a href="{{ route('admin.usuarios.index') }}" @class([
            'block px-3 py-1.5 rounded-lg transition-colors text-sm',
            'bg-white/15 text-white font-semibold shadow-sm ring-1 ring-white/20' => request()->routeIs('admin.usuarios.*'),
            'text-rose-100/85 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.usuarios.*'),
        ])>
            Usuarios
        </a>
    </div>
</div>

<!-- Grupo: Operación -->
<div class="sidebar-group pt-1" data-group="operacion">
    <button type="button" onclick="toggleSidebarGroup('operacion')"
        class="w-full flex items-center justify-between px-4 py-2 rounded-lg text-xs font-semibold uppercase tracking-wider text-rose-200/80 hover:bg-white/10 hover:text-white transition-colors">
        <span>Operación</span>
        <svg data-chevron="operacion" class="w-4 h-4 transition-transform duration-200 {{ $enOperacion ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <div id="sidebar-group-operacion" class="mt-1 space-y-1 pl-2 border-l border-white/15 ml-4 {{ $enOperacion ? '' : 'hidden' }}">
        <a href="{{ route('admin.importaciones.index') }}" @class([
            'block px-3 py-1.5 rounded-lg transition-colors text-sm',
            'bg-white/15 text-white font-semibold shadow-sm ring-1 ring-white/20' => request()->routeIs('admin.importaciones.*'),
            'text-rose-100/85 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.importaciones.*'),
        ])>
            Importaciones
        </a>
        <a href="{{ route('admin.participantes.index') }}" @class([
            'block px-3 py-1.5 rounded-lg transition-colors text-sm',
            'bg-white/15 text-white font-semibold shadow-sm ring-1 ring-white/20' => request()->routeIs('admin.participantes.*'),
            'text-rose-100/85 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.participantes.*'),
        ])>
            Participantes
        </a>
        <a href="{{ route('admin.inscripciones.index') }}" @class([
            'block px-3 py-1.5 rounded-lg transition-colors text-sm',
            'bg-white/15 text-white font-semibold shadow-sm ring-1 ring-white/20' => request()->routeIs('admin.inscripciones.*'),
            'text-rose-100/85 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.inscripciones.*'),
        ])>
            Inscripciones
        </a>
        <a href="{{ route('admin.presentaciones.index') }}" @class([
            'block px-3 py-1.5 rounded-lg transition-colors text-sm',
            'bg-white/15 text-white font-semibold shadow-sm ring-1 ring-white/20' => request()->routeIs('admin.presentaciones.*'),
            'text-rose-100/85 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.presentaciones.*'),
        ])>
            Presentaciones
        </a>
        <a href="{{ route('admin.jurados.index') }}" @class([
            'block px-3 py-1.5 rounded-lg transition-colors text-sm',
            'bg-white/15 text-white font-semibold shadow-sm ring-1 ring-white/20' => request()->routeIs('admin.jurados.index'),
            'text-rose-100/85 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.jurados.index'),
        ])>
            Jurados
        </a>
        <a href="{{ route('admin.jurados.secreto') }}" @class([
            'block px-3 py-1.5 rounded-lg transition-colors text-sm',
            'bg-white/15 text-white font-semibold shadow-sm ring-1 ring-white/20' => request()->routeIs('admin.jurados.secreto'),
            'text-rose-100/85 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.jurados.secreto'),
        ])>
            🔒 Jurado Secreto
        </a>
    </div>
</div>

<!-- Grupo: Resultados -->
<div class="sidebar-group pt-1" data-group="resultados">
    <button type="button" onclick="toggleSidebarGroup('resultados')"
        class="w-full flex items-center justify-between px-4 py-2 rounded-lg text-xs font-semibold uppercase tracking-wider text-rose-200/80 hover:bg-white/10 hover:text-white transition-colors">
        <span>Resultados</span>
        <svg data-chevron="resultados" class="w-4 h-4 transition-transform duration-200 {{ $enResultados ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <div id="sidebar-group-resultados" class="mt-1 space-y-1 pl-2 border-l border-white/15 ml-4 {{ $enResultados ? '' : 'hidden' }}">
        <a href="{{ route('inicio') }}" target="_blank" class="px-3 py-1.5 rounded-lg text-sm text-rose-100/85 hover:bg-white/10 hover:text-white flex items-center justify-between transition-colors">
            <span>Ranking Público</span>
            <span class="text-[10px] text-amber-300">↗</span>
        </a>
        <a href="{{ route('admin.reportes.index') }}" @class([
            'block px-3 py-1.5 rounded-lg transition-colors text-sm',
            'bg-white/15 text-white font-semibold shadow-sm ring-1 ring-white/20' => request()->routeIs('admin.reportes.*'),
            'text-rose-100/85 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.reportes.*'),
        ])>
            Reportes & Actas
        </a>
        <a href="{{ route('admin.auditoria.index') }}" @class([
            'block px-3 py-1.5 rounded-lg transition-colors text-sm',
            'bg-white/15 text-white font-semibold shadow-sm ring-1 ring-white/20' => request()->routeIs('admin.auditoria.*'),
            'text-rose-100/85 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.auditoria.*'),
        ])>
            Auditoría
        </a>
    </div>
</div>

<script>
    function toggleSidebarGroup(group) {
        const groups = ['festival', 'operacion', 'resultados'];
        const targetEl = document.getElementById('sidebar-group-' + group);
        const isCurrentlyHidden = targetEl?.classList.contains('hidden');

        groups.forEach((g) => {
            const el = document.getElementById('sidebar-group-' + g);
            const chevron = document.querySelector('[data-chevron="' + g + '"]');
            if (!el) return;

            if (g === group && isCurrentlyHidden) {
                el.classList.remove('hidden');
                chevron?.classList.add('rotate-180');
            } else {
                el.classList.add('hidden');
                chevron?.classList.remove('rotate-180');
            }
        });
    }
</script>
