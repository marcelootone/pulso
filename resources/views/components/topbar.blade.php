<header class="bg-white border-b border-gray-200 sticky top-0 z-20">
    <div class="flex items-center justify-between px-4 sm:px-6 h-16">
        <div class="flex items-center flex-1">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:bg-gray-100 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 mr-4 transition-colors">
                <x-heroicon-o-bars-3 class="w-6 h-6" />
            </button>

            <!-- Busca Global (abre o modal via Ctrl+K ou clique) -->
            <button type="button"
                    onclick="window.dispatchEvent(new CustomEvent('open-search'))"
                    class="hidden sm:flex items-center w-full max-w-md text-left bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
                    aria-label="Pesquisar no sistema (Ctrl+K)">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 mr-2 flex-shrink-0" />
                <span class="flex-1 text-sm text-gray-400 truncate">Pesquise por Turmas, Estudantes, Funcionários ou Espaços...</span>
                <span class="ml-2 hidden md:inline-flex items-center text-xs font-semibold text-gray-400 border border-gray-200 rounded px-1.5 py-0.5 bg-white">Ctrl&nbsp;K</span>
            </button>

            <!-- Versão compacta para telas pequenas (somente a lupa) -->
            <button type="button"
                    onclick="window.dispatchEvent(new CustomEvent('open-search'))"
                    class="sm:hidden text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
                    aria-label="Pesquisar no sistema">
                <x-heroicon-o-magnifying-glass class="w-6 h-6" />
            </button>
        </div>

        <!-- Direita: Notificações e Perfil -->
        <div class="flex items-center space-x-4">
            
            <!-- Notificações (Alpine Component) -->
            <div x-data="notificationsComponent()" x-init="fetchNotifications()" class="relative">
                <button @click="open = !open" @click.outside="open = false" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full relative transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <span class="sr-only">Ver notificações</span>
                    <x-heroicon-o-bell class="w-6 h-6" />
                    <span x-show="unreadCount > 0" class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5" x-cloak>
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 ring-2 ring-white"></span>
                    </span>
                </button>

                <!-- Dropdown -->
                <div x-show="open" 
                     x-transition
                     x-cloak
                     class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 ring-1 ring-gray-200 z-50 origin-top-right">
                    <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-md">
                        <h3 class="text-sm font-semibold text-gray-900">Notificações</h3>
                        <button x-show="unreadCount > 0" @click="markAllAsRead" class="text-xs font-medium text-primary-600 hover:text-primary-800">
                            Marcar todas como lidas
                        </button>
                    </div>
                    
                    <div class="max-h-96 overflow-y-auto">
                        <template x-if="notifications.length === 0">
                            <div class="px-4 py-8 flex flex-col items-center justify-center text-center">
                                <x-heroicon-o-bell-slash class="w-10 h-10 text-gray-300 mb-2" />
                                <p class="text-sm text-gray-500">Você não tem novas notificações.</p>
                            </div>
                        </template>

                        <template x-for="notification in notifications" :key="notification.id">
                            <div class="px-4 py-3 hover:bg-gray-50 flex items-start border-b border-gray-50 last:border-0 relative group transition-colors">
                                <div class="flex-1 min-w-0 pr-4">
                                    <p class="text-sm text-gray-800 line-clamp-2" x-text="notification.data.message || 'Nova notificação'"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="notification.created_at"></p>
                                </div>
                                <button @click="markAsRead(notification.id)" class="flex-shrink-0 text-gray-300 hover:text-primary-600 opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none" title="Marcar como lida">
                                    <x-heroicon-o-check-circle class="w-5 h-5" />
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Perfil Dropdown -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center p-2 rounded-md hover:bg-gray-100 text-sm font-medium text-gray-700 transition-colors focus:outline-none">
                        @if(Auth::check())
                            <div class="mr-2">{{ Auth::user()->name }}</div>
                        @endif
                        <x-heroicon-s-chevron-down class="w-4 h-4 text-gray-500" />
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('users.show', Auth::user()->id)">
                        {{ __('Meu Perfil') }}
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Sair') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</header>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('notificationsComponent', () => ({
            open: false,
            notifications: [],
            unreadCount: 0,
            
            async fetchNotifications() {
                try {
                    const response = await fetch('/api/notifications');
                    const data = await response.json();
                    this.notifications = data.notifications || [];
                    this.unreadCount = this.notifications.length;
                } catch (error) {
                    console.error('Erro ao buscar notificações:', error);
                }
            },
            
            async markAsRead(id) {
                try {
                    await fetch(`/api/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    this.notifications = this.notifications.filter(n => n.id !== id);
                    this.unreadCount = this.notifications.length;
                } catch (error) {
                    console.error('Erro ao marcar como lida:', error);
                }
            },
            
            async markAllAsRead() {
                try {
                    await fetch('/api/notifications/read-all', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    this.notifications = [];
                    this.unreadCount = 0;
                    this.open = false;
                } catch (error) {
                    console.error('Erro ao marcar todas como lidas:', error);
                }
            }
        }));
    });
</script>
