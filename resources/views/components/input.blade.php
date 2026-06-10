@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm sm:text-sm disabled:opacity-50 disabled:bg-gray-50']) !!}>
