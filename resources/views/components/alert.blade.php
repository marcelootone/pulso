@props(['type' => 'info', 'message' => null])

@php
    $types = [
        'success' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-200',
            'text' => 'text-green-800',
            'icon' => 'text-green-400',
            'iconName' => 'o-check-circle'
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'text' => 'text-red-800',
            'icon' => 'text-red-400',
            'iconName' => 'o-x-circle'
        ],
        'warning' => [
            'bg' => 'bg-yellow-50',
            'border' => 'border-yellow-200',
            'text' => 'text-yellow-800',
            'icon' => 'text-yellow-400',
            'iconName' => 'o-exclamation-triangle'
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'text' => 'text-blue-800',
            'icon' => 'text-blue-400',
            'iconName' => 'o-information-circle'
        ],
    ];
    $style = $types[$type];
@endphp

<div {{ $attributes->merge(['class' => "flex items-start p-4 rounded-md border {$style['bg']} {$style['border']}"]) }}>
    <div class="flex-shrink-0">
        <x-dynamic-component :component="'heroicon-' . $style['iconName']" class="h-5 w-5 {{ $style['icon'] }}" />
    </div>
    <div class="ml-3 w-full">
        <p class="text-sm font-medium {{ $style['text'] }}">
            {{ $message ?? $slot }}
        </p>
    </div>
</div>
