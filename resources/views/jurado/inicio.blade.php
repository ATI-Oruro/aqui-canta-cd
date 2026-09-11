<x-layouts.jurado>
    <x-slot:header>
        <h2 class="text-xl font-semibold text-slate-800">Panel de Jurado</h2>
    </x-slot:header>

    <x-slot:sidebar>
        <a href="{{ route('jurado.inicio') }}" class="block px-4 py-2 rounded-lg bg-slate-700 text-white font-semibold">Inicio</a>
        <a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white">Mis presentaciones</a>
        <a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white">Calificar</a>
        <a href="#" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white">Cronómetro</a>
    </x-slot:sidebar>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl">
        <h3 class="font-semibold text-slate-800">Bienvenido/a, {{ auth()->user()->nombre }}</h3>
        <p class="mt-2 text-sm text-slate-500">
            Aquí visualizarás la presentación actual, el cronómetro y podrás registrar tus calificaciones
            cuando el festival esté en operación (Fases 3-4).
        </p>
        <div class="mt-4 text-xs text-slate-400">
            Estado: por asignar presentaciones
        </div>
    </div>
</x-layouts.jurado>