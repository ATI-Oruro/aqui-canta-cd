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
<a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Importaciones</a>
<a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Presentaciones</a>
<a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Jurados</a>

<span class="block px-4 pt-4 pb-1 text-xs uppercase tracking-wider text-slate-500">Resultados</span>
<a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Ranking</a>
<a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Reportes</a>
<a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Auditoría</a>