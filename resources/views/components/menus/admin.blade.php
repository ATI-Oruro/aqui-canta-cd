<a href="{{ route('admin.inicio') }}" @class(['block px-4 py-2 rounded-lg', 'bg-slate-800 text-white font-semibold' => request()->routeIs('admin.inicio'), 'text-slate-300 hover:bg-slate-800 hover:text-white' => ! request()->routeIs('admin.inicio')])>
    Inicio
</a>

<span class="block px-4 pt-4 pb-1 text-xs uppercase tracking-wider text-slate-500">Festival</span>
<a href="{{ route('admin.configuracion') }}" @class(['block px-4 py-2 rounded-lg', 'bg-slate-800 text-white font-semibold' => request()->routeIs('admin.configuracion'), 'text-slate-300 hover:bg-slate-800 hover:text-white' => ! request()->routeIs('admin.configuracion')])>
    Configuración
</a>
<a href="{{ route('admin.usuarios.index') }}" @class(['block px-4 py-2 rounded-lg', 'bg-slate-800 text-white font-semibold' => request()->routeIs('admin.usuarios.*'), 'text-slate-300 hover:bg-slate-800 hover:text-white' => ! request()->routeIs('admin.usuarios.*')])>
    Usuarios
</a>

<span class="block px-4 pt-4 pb-1 text-xs uppercase tracking-wider text-slate-500">Operación</span>
<a href="{{ route('admin.importaciones.index') }}" @class(['block px-4 py-2 rounded-lg', 'bg-slate-800 text-white font-semibold' => request()->routeIs('admin.importaciones.*'), 'text-slate-300 hover:bg-slate-800 hover:text-white' => ! request()->routeIs('admin.importaciones.*')])>
    Importaciones
</a>
<a href="{{ route('admin.participantes.index') }}" @class(['block px-4 py-2 rounded-lg', 'bg-slate-800 text-white font-semibold' => request()->routeIs('admin.participantes.*'), 'text-slate-300 hover:bg-slate-800 hover:text-white' => ! request()->routeIs('admin.participantes.*')])>
    Participantes
</a>
<a href="{{ route('admin.inscripciones.index') }}" @class(['block px-4 py-2 rounded-lg', 'bg-slate-800 text-white font-semibold' => request()->routeIs('admin.inscripciones.*'), 'text-slate-300 hover:bg-slate-800 hover:text-white' => ! request()->routeIs('admin.inscripciones.*')])>
    Inscripciones
</a>
<a href="{{ route('admin.presentaciones.index') }}" @class(['block px-4 py-2 rounded-lg', 'bg-slate-800 text-white font-semibold' => request()->routeIs('admin.presentaciones.*'), 'text-slate-300 hover:bg-slate-800 hover:text-white' => ! request()->routeIs('admin.presentaciones.*')])>
    Presentaciones
</a>
<a href="{{ route('admin.jurados.index') }}" @class(['block px-4 py-2 rounded-lg', 'bg-slate-800 text-white font-semibold' => request()->routeIs('admin.jurados.*'), 'text-slate-300 hover:bg-slate-800 hover:text-white' => ! request()->routeIs('admin.jurados.*')])>
    Jurados
</a>

<span class="block px-4 pt-4 pb-1 text-xs uppercase tracking-wider text-slate-500">Resultados</span>
<a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Ranking</a>
<a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Reportes</a>
<a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Auditoría</a>