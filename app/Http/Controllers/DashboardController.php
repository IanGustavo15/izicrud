<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'dadosEstatisticas' => $this->obterDadosEstatisticas(),
            'dadosGraficoReceita' => $this->obterGraficoReceita(),
            'dadosGraficoUsuarios' => $this->obterGraficoUsuarios(),
            'dadosCategorias' => $this->obterGraficoCategorias(),
            'dadosMelhoresProfissionais' => $this->obterTabelaPerformers(),
            'dadosOrdensRecentes' => $this->obterTabelaPedidos(),
            'dadosServicos' => $this->obterTabelaServicos(),
            'receitaTotal' => $this->receitaTotal(),
            'valorMedioPedido' => $this->valorMedioPedido(),
            'servicosAtivos' => $this->servicosAtivos(),
        ]);
    }

    private function obterDadosEstatisticas()
    {
        return [
            [
                'title' => 'Total de Registros',
                'value' => '2,847',
                'change' => '+12.5%',
                'subtitle' => 'últimos 30 dias',
                'variant' => 'success'
            ],
            [
                'title' => 'Novos Hoje',
                'value' => '23',
                'change' => '+5.2%',
                'subtitle' => 'desde ontem',
                'variant' => 'default'
            ],
            [
                'title' => 'Ativos',
                'value' => '89%',
                'change' => '+2.1%',
                'subtitle' => 'taxa de atividade',
                'variant' => 'success'
            ],
            [
                'title' => 'Processando',
                'value' => '156',
                'change' => '-1.3%',
                'subtitle' => 'na fila',
                'variant' => 'warning'
            ]
        ];
    }

    private function obterGraficoReceita()
    {
        return [
            [
                'label' => 'Jan',
                 'value' => 4200
            ],
            [
                'label' => 'Fev',
                 'value' => 3800
            ],
            [
                'label' => 'Mar',
                 'value' => 5200
            ],
            [
                'label' => 'Abr',
                 'value' => 4800
            ],
            [
                'label' => 'Mai',
                 'value' => 6100
            ],
            [
                'label' => 'Jun',
                 'value' => 5500
            ],
            [
                'label' => 'Jul',
                 'value' => 7200
            ]
        ];
    }

    private function obterGraficoUsuarios()
    {
        return [
            [
                'label' => 'Seg'
                , 'value' => 150
            ],
            [
                'label' => 'Ter',
                'value' => 190
            ],
            [
                'label' => 'Qua',
                'value' => 300
            ],
            [
                'label' => 'Qui',
                'value' => 250
            ],
            [
                'label' => 'Sex',
                'value' => 420
            ],
            [
                'label' => 'Sab',
                'value' => 350
            ],
            [
                'label' => 'Dom',
                'value' => 280
            ]
        ];
    }

    private function obterGraficoCategorias()
    {
        return [
            [
                'label' => 'Cães',
                'value' => 234
            ],
            [
                'label' => 'Gatos',
                'value' => 135
            ],
            [
                'label' => 'Exóticos',
                'value' => 45
            ]
        ];
    }

    private function obterTabelaPerformers()
    {
        return [
            'columns' => [
                [
                    'key' => 'avatar',
                    'label' => 'Profissional'
                ],
                [
                    'key' => 'especialidade',
                    'label' => 'Especialidade'
                ],
                [
                    'key' => 'consultas',
                    'label' => 'Consultas'
                ],
                [
                    'key' => 'rating',
                    'label' => 'Avaliação'
                ],
                [
                    'key' => 'trend',
                    'label' => 'Tendência'
                ]
            ],
            'data' => [
                [
                    'avatar' => true,
                    'nome' => 'Dr. Carlos Silva',
                    'especialidade' => 'Cardiologia',
                    'consultas' => 47,
                    'rating' => 4.9, 'trend' => 12.5
                ],
                [
                    'avatar' => true,
                    'nome' => 'Dra. Ana Santos',
                    'especialidade' => 'Dermatologia',
                    'consultas' => 42,
                    'rating' => 4.8,
                    'trend' => 8.3
                ],
                [
                    'avatar' => true,
                    'nome' => 'Dr. Pedro Lima',
                    'especialidade' => 'Cirurgia',
                    'consultas' => 38,
                    'rating' => 4.7,
                    'trend' => -2.1
                ],
                [
                    'avatar' => true,
                    'nome' => 'Dra. Maria Costa',
                    'especialidade' => 'Clínica Geral',
                    'consultas' => 35,
                    'rating' => 4.6,
                    'trend' => 5.7
                ]
            ]
        ];
    }

    private function obterTabelaPedidos()
    {
        return [
            'columns' => [
                [
                    'key' => 'numero',
                     'label' => 'Ordem'
                ],
                [
                    'key' => 'pet',
                    'label' => 'Pet'
                ],
                [
                    'key' => 'servico',
                    'label' => 'Serviço'
                ],
                [
                    'key' => 'status',
                    'label' => 'Status'
                ],
                [
                    'key' => 'total',
                    'label' => 'Total'
                ]
            ],
            'data' => [
                [
                    'numero' => 'OS-001',
                    'pet' => 'Buddy (Golden)',
                    'servico' => 'Consulta + Vacina',
                    'status' => 'concluído',
                    'total' => 150.00,
                    'data' => '24/11/2024'
                ],
                [
                    'numero' => 'OS-002',
                    'pet' => 'Luna (Persa)',
                    'servico' => 'Banho e Tosa',
                    'status' => 'em_andamento',
                    'total' => 80.00,
                    'data' => '24/11/2024'
                ],
                [
                    'numero' => 'OS-003',
                    'pet' => 'Rex (Pastor)',
                    'servico' => 'Cirurgia',
                    'status' => 'pendente',
                    'total' => 350.00,
                    'data' => '23/11/2024'
                ],
                [
                    'numero' => 'OS-004',
                    'pet' => 'Mimi (SRD)',
                    'servico' => 'Exame',
                    'status' => 'concluído',
                    'total' => 120.00,
                    'data' => '23/11/2024'
                ]
            ]
        ];
    }

    private function obterTabelaServicos()
    {
        return [
            'columns' => [
                [
                    'key' => 'nome',
                    'label' => 'Serviço'
                ],
                [
                    'key' => 'categoria',
                    'label' => 'Categoria'
                ],
                [
                    'key' => 'preco',
                    'label' => 'Preço'
                ],
                [
                    'key' => 'agendamentos',
                    'label' => 'Agendamentos'
                ],
                [
                    'key' => 'status',
                    'label' => 'Status'
                ]
            ],
            'data' => [
                [
                    'nome' => 'Consulta Veterinária',
                    'categoria' => 'Consulta',
                    'preco' => 80.00,
                    'status' => 'ativo',
                    'agendamentos' => 156
                ],
                [
                    'nome' => 'Vacinação Múltipla',
                    'categoria' => 'Preventivo',
                    'preco' => 120.00,
                    'status' => 'ativo',
                    'agendamentos' => 89
                ],
                [
                    'nome' => 'Cirurgia Castração',
                    'categoria' => 'Cirurgia',
                    'preco' => 350.00,
                    'status' => 'ativo',
                    'agendamentos' => 23
                ],
                [
                    'nome' => 'Banho e Tosa',
                    'categoria' => 'Estética',
                    'preco' => 50.00,
                    'status' => 'ativo',
                    'agendamentos' => 67
                ]
            ]
        ];
    }

    private function receitaTotal()
    {
        return array_sum(array_column($this->obterGraficoReceita(), 'value'));
    }

    private function valorMedioPedido()
    {
        $tabelaPedidos = $this->obterTabelaPedidos();
        $pedidos = $tabelaPedidos['data'];
        $total = array_sum(array_column($pedidos, 'total'));
        return $total / count($pedidos);
    }

    private function servicosAtivos()
    {
        $tabelaServicos = $this->obterTabelaServicos();
        return count(array_filter($tabelaServicos['data'], fn($servico) => $servico['status'] === 'ativo'));
    }
}
