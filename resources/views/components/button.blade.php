@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200';
    
    $variants = [
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 border border-transparent',
        'secondary' => 'bg-white text-gray-700 hover:bg-gray-50 focus:ring-primary-500 border border-gray-300 shadow-sm',
        'danger' => 'bg-danger text-white hover:bg-red-700 focus:ring-red-500 border border-transparent',
        'ghost' => 'bg-transparent text-primary-600 hover:bg-primary-50 focus:ring-primary-500 border border-transparent',
    ];
    
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
    
    $classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
