<x-layouts.admin>
    <x-slot:header>
        <h2 class="text-xl font-semibold text-slate-800">Panel de Administración</h2>
    </x-slot:header>

    <x-slot:sidebar>
        <a href="{{ route('admin.inicio') }}" class="block px-4 py-2 rounded-lg bg-slate-800 text-white font-semibold">Inicio</a>
        <span class="block px-4 pt-4 pb-1 text-xs uppercase tracking-wider text-slate-500">Festival</span>
        <a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Categorías</a>
        <a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Parámetros</a>
        <a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Departamentos</a>
        <a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Usuarios</a>
        <span class="block px-4 pt-4 pb-1 text-xs uppercase tracking-wider text-slate-500">Operación</span>
        <a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Importaciones</a>
        <a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Presentaciones</a>
        <a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Jurados</a>
        <span class="block px-4 pt-4 pb-1 text-xs uppercase tracking-wider text-slate-500">Resultados</span>
        <a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Ranking</a>
        <a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Reportes</a>
        <a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">Auditoría</a>
    </x-slot:sidebar>

    <div class="grid gap-6 md:grid-cols-3">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="font-semibold text-slate-800">Configuración</h3>
            <p class="mt-1 text-sm text-slate-500">Categorías, parámetros, departamentos y usuarios del festival (Fase 1).</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="font-semibold text-slate-800">Operación</h3>
            <p class="mt-1 text-sm text-slate-500">Importaciones, presentaciones, jurados y cronómetro en vivo (Fases 2-3).</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="font-semibold text-slate-800">Resultados</h3>
            <p class="mt-1 text-sm text-slate-500">Calificaciones, ranking, revelación de jurado secreto y reportes (Fases 4-7).</p>
        </div>
    </div>
</x-layouts.admin>