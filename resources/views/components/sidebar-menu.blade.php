<nav aria-label="Menu principal" class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
    @foreach ($menu as $item)
        @if(isset($item['children']) && count($item['children']) > 0)
            <div x-data="{ expanded: false }" class="space-y-1">
                <!-- Group Header -->
                <button @click="expanded = !expanded"
                        type="button"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors">
                    <div class="flex items-center">
                        @if(isset($item['icon']))
                            <x-icon :name="'heroicon-' . $item['icon']" class="w-5 h-5 mr-3 flex-shrink-0" />
                        @endif
                        <span x-show="sidebarOpen" x-transition class="truncate">{{ $item['label'] }}</span>
                    </div>
                    <x-heroicon-s-chevron-down x-show="sidebarOpen"
                                               class="w-4 h-4 transition-transform duration-200 flex-shrink-0"
                                               x-bind:class="{ 'rotate-180': expanded }" />
                </button>
                <!-- Children -->
                <div x-show="expanded && sidebarOpen" x-collapse x-cloak class="pl-11 pr-2 space-y-1">
                    @foreach($item['children'] as $child)
                        <a href="{{ isset($child['route']) && Route::has($child['route']) ? route($child['route']) : '#' }}"
                           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ isset($child['route']) && request()->routeIs($child['route']) ? 'bg-primary-900 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} transition-colors">
                            {{ $child['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Single Link -->
            <a href="{{ isset($item['route']) && Route::has($item['route']) ? route($item['route']) : '#' }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ isset($item['route']) && request()->routeIs($item['route']) ? 'bg-primary-900 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} transition-colors">
                @if(isset($item['icon']))
                    <x-icon :name="'heroicon-' . $item['icon']" class="w-5 h-5 mr-3 flex-shrink-0" />
                @endif
                <span x-show="sidebarOpen" x-transition class="truncate">{{ $item['label'] }}</span>
            </a>
        @endif
    @endforeach
</nav>
