@props(['items' => []])

@if(!empty($items))
<nav class="flex text-sm font-medium mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        @foreach($items as $index => $item)
            <li class="inline-flex items-center">
                @if($index > 0)
                    <x-heroicon-s-chevron-right class="w-4 h-4 mx-1 text-gray-400" />
                @endif
                
                @if(isset($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}" class="inline-flex items-center text-gray-500 hover:text-primary-600 transition-colors">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-gray-900" aria-current="page">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
