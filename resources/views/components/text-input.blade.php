@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#5c0618] focus:ring-[#5c0618] rounded-md shadow-sm']) }}>
