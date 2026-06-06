@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-stormy-500 focus:ring-stormy-500 rounded-md shadow-sm']) }}>
