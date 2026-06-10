<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SidebarMenu extends Component
{
    public array $menu;

    public function __construct()
    {
        $this->menu = $this->buildMenu(config('sigae-menu', []));
    }

    public function render(): View|Closure|string
    {
        return view('components.sidebar-menu');
    }

    private function buildMenu(array $items): array
    {
        $filtered = [];
        $user = auth()->user();

        if (!$user) {
            return [];
        }

        foreach ($items as $item) {
            if ($this->userCanAccess($user, $item)) {
                if (isset($item['children'])) {
                    $item['children'] = $this->buildMenu($item['children']);
                    
                    // Se o item for um grupo (não tem rota) e ficou sem filhos após o filtro, esconde
                    if (empty($item['children']) && empty($item['route'])) {
                        continue;
                    }
                }
                $filtered[] = $item;
            }
        }

        return $filtered;
    }

    private function userCanAccess($user, array $item): bool
    {
        // 1. Checagem por roles (legado/fallback)
        if (isset($item['roles']) && !empty($item['roles'])) {
            if (in_array('*', $item['roles'])) {
                return true;
            }
            if ($user->hasAnyRole($item['roles'])) {
                return true;
            }
        }

        // 2. Checagem por permissões granulares (novo padrão)
        if (isset($item['permissions']) && !empty($item['permissions'])) {
            foreach ($item['permissions'] as $permission) {
                if ($user->can($permission)) {
                    return true;
                }
            }
        }

        return false;
    }
}
