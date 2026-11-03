<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#5c0618] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#7a0920] focus:bg-[#7a0920] active:bg-[#3d040f] focus:outline-none focus:ring-2 focus:ring-[#5c0618] focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm']) }}>
    {{ $slot }}
</button>
