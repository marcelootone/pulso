<div x-data="searchModal()" 
     @keydown.window="
        if (($event.ctrlKey || $event.metaKey) && $event.key === 'k') { 
            $event.preventDefault(); 
            open = true; 
        }
        if ($event.key === 'Escape') { 
            open = false; 
        }
     "
     @open-search.window="open = true"
     x-cloak>
    
    <!-- Modal Backdrop -->
    <div x-show="open" 
         x-transition.opacity 
         class="fixed inset-0 z-50 flex items-start justify-center pt-16 sm:pt-24 bg-gray-900/50 backdrop-blur-sm"
         @click="open = false"
         style="display: none;">
         
        <!-- Modal Content -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             x-trap="open"
             class="w-full max-w-2xl bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[80vh]">
            
            <!-- Input Area -->
            <div class="relative flex items-center border-b border-gray-100 px-4">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" 
                       x-model="query" 
                       x-ref="searchInput"
                       @input.debounce.300ms="fetchResults"
                       class="w-full border-0 focus:ring-0 py-4 pl-4 pr-12 text-gray-900 placeholder-gray-400 text-lg bg-transparent"
                       placeholder="Buscar por alunos, turmas ou funcionários..."
                       autocomplete="off">
                <button @click="open = false" class="absolute right-4 p-1 text-gray-400 hover:text-gray-500 rounded-md focus:outline-none">
                    <span class="text-xs font-semibold bg-gray-100 px-2 py-1 rounded">ESC</span>
                </button>
            </div>

            <!-- Results Area -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4" x-show="query.length >= 2">
                
                <div x-show="isLoading" class="flex justify-center py-8">
                    <svg class="animate-spin h-6 w-6 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <div x-show="!isLoading && Object.keys(results).length === 0" class="text-center py-12" style="display: none;">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-4 text-sm text-gray-500">Nenhum resultado encontrado para "<span x-text="query" class="font-medium text-gray-900"></span>".</p>
                </div>

                <template x-for="(items, category) in results" :key="category">
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2" x-text="category"></h3>
                        <ul class="space-y-1">
                            <template x-for="item in items" :key="item.id">
                                <li>
                                    <a :href="item.url" class="flex items-center px-4 py-3 rounded-lg hover:bg-primary-50 hover:text-primary-700 transition-colors group">
                                        <div class="flex-shrink-0 h-8 w-8 bg-gray-100 group-hover:bg-primary-100 text-gray-500 group-hover:text-primary-600 rounded flex items-center justify-center mr-3 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 group-hover:text-primary-700" x-text="item.title"></p>
                                            <p class="text-xs text-gray-500 group-hover:text-primary-500" x-text="item.subtitle"></p>
                                        </div>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>
            
            <div x-show="query.length < 2" class="p-8 text-center text-gray-500 text-sm">
                Digite pelo menos 2 caracteres para buscar.
            </div>
            
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('searchModal', () => ({
            open: false,
            query: '',
            results: {},
            isLoading: false,
            
            init() {
                this.$watch('open', value => {
                    if (value) {
                        setTimeout(() => this.$refs.searchInput.focus(), 100);
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                        this.query = '';
                        this.results = {};
                    }
                });
            },
            
            async fetchResults() {
                if (this.query.length < 2) {
                    this.results = {};
                    return;
                }
                
                this.isLoading = true;
                
                try {
                    const response = await fetch(`/api/search?q=${encodeURIComponent(this.query)}`);
                    const data = await response.json();
                    this.results = data.results;
                } catch (error) {
                    console.error("Erro na busca:", error);
                    this.results = {};
                } finally {
                    this.isLoading = false;
                }
            }
        }))
    });
</script>
