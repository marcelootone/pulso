<?php

/**
 * Configuração centralizada do menu lateral do SIGAE.
 * Suporta checagem por 'roles' (perfis) e 'permissions' (permissões granulares).
 * 
 * Se 'permissions' estiver definido, o menu usa `@canany`.
 * Se apenas 'roles' estiver definido, o menu usa `@hasanyrole`.
 * Se 'roles' contiver '*', o menu é exibido para todos os usuários autenticados.
 */

return [
    [
        'label' => 'Dashboard',
        'icon' => 'o-chart-pie',
        'route' => 'dashboard',
        'roles' => ['*'],
    ],

    [
        'label' => 'Acadêmico',
        'icon' => 'o-academic-cap',
        'roles' => ['Gestor', 'Secretaria', 'Coordenador', 'Professor', 'Professor Educação Especial', 'Professor de Estudo Orientado'],
        'children' => [
            [
                'label' => 'Turmas',
                'route' => 'turmas.index',
                'roles' => ['Gestor', 'Secretaria', 'Coordenador', 'Professor', 'Professor Educação Especial', 'Professor de Estudo Orientado'],
                'permissions' => ['gerenciar turmas', 'acessar turmas vinculadas'],
            ],
            [
                'label' => 'Alunos',
                'route' => 'alunos.index',
                'roles' => ['Gestor', 'Secretaria', 'Coordenador'],
                'permissions' => ['gerenciar estudantes', 'ver estudantes'],
            ],


        ],
    ],

    [
        'label' => 'Pedagógico',
        'icon' => 'o-book-open',
        'roles' => ['Gestor', 'Secretaria', 'Coordenador', 'Professor', 'Professor Educação Especial', 'Professor de Estudo Orientado'],
        'children' => [
            [
                'label' => 'Meu Diário',
                'route' => 'diario.index',
                'roles' => ['Gestor', 'Coordenador', 'Professor', 'Professor Educação Especial', 'Professor de Estudo Orientado'],
                'permissions' => ['acessar turmas vinculadas', 'ver todos diarios'],
            ],
            [
                'label' => 'Frequência Escolar',
                'route' => 'frequencia.index',
                'roles' => ['Gestor', 'Secretaria', 'Coordenador'],
                'permissions' => ['ver frequencia geral', 'acompanhar frequencia'],
            ],
        ],
    ],

    [
        'label' => 'Planejamento Semanal',
        'icon' => 'o-calendar',
        'route' => 'planejamento.index',
        'roles' => ['Gestor', 'Coordenador', 'Professor', 'Professor Educação Especial', 'Professor de Estudo Orientado'],
        'permissions' => ['gerenciar horarios'],
    ],

    [
        'label' => 'Eletivas e Clubes',
        'icon' => 'o-puzzle-piece',
        'route' => 'eletivas.index',
        'roles' => ['Gestor', 'Secretaria', 'Coordenador', 'Professor', 'Professor Educação Especial', 'Professor de Estudo Orientado'],
        'permissions' => ['gerenciar eletivas', 'acessar proprias eletivas'],
    ],

    [
        'label' => 'Estudo Orientado',
        'icon' => 'o-light-bulb',
        'roles' => ['Gestor', 'Secretaria', 'Coordenador', 'Professor', 'Professor Educação Especial', 'Professor de Estudo Orientado'],
        'children' => [
            [
                'label' => 'Solicitações',
                'route' => 'estudo-orientado.solicitacoes.index',
                'roles' => ['Gestor', 'Secretaria', 'Coordenador', 'Professor', 'Professor Educação Especial'],
                'permissions' => ['criar solicitacao estudo orientado', 'consultar estudo orientado', 'analisar solicitacao estudo orientado'],
            ],
            [
                'label' => 'Análises',
                'route' => 'estudo-orientado.analises.index',
                'roles' => ['Gestor', 'Coordenador'],
                'permissions' => ['analisar solicitacao estudo orientado'],
            ],
            [
                'label' => 'Acompanhamentos',
                'route' => 'estudo-orientado.acompanhamentos.index',
                'roles' => ['Gestor', 'Coordenador', 'Professor de Estudo Orientado'],
                'permissions' => ['registrar atendimento estudo orientado'],
            ],
            [
                'label' => 'Relatórios',
                'route' => 'estudo-orientado.relatorios',
                'roles' => ['Gestor', 'Coordenador'],
                'permissions' => ['consultar estudo orientado'],
            ],
        ],
    ],

    [
        'label' => 'Agendamentos',
        'icon' => 'o-calendar-days',
        'route' => 'agendamentos.index',
        'roles' => ['*'],
        'permissions' => ['agendar espacos'],
    ],

    [
        'label' => 'Relatórios',
        'icon' => 'o-document-chart-bar',
        'route' => 'relatorios.index',
        'roles' => ['Gestor', 'Secretaria', 'Coordenador'],
        'permissions' => ['ver relatorios administrativos', 'ver relatorios pedagogicos'],
    ],

    [
        'label' => 'Central de Cadastros',
        'icon' => 'o-server-stack',
        'roles' => ['Gestor', 'Secretaria', 'Coordenador'],
        'children' => [
            [
                'label' => 'Funcionários',
                'route' => 'users.index',
                'roles' => ['Gestor', 'Secretaria', 'Coordenador'],
                'permissions' => ['gerenciar professores'],
            ],
            [
                'label' => 'Espaços',
                'route' => 'espacos.index',
                'roles' => ['Gestor', 'Secretaria'],
                'permissions' => ['gerenciar espacos'],
            ],

            [
                'label' => 'Importar Estudantes Planilha',
                'route' => 'importar.index',
                'roles' => ['Gestor', 'Secretaria'],
                'permissions' => ['gerenciar estudantes'],
            ],
        ],
    ],
];
