<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\OrdemDeServico;
use App\Models\Servico;
use App\Models\Veiculo;
use App\Models\Cliente;
use App\Models\Peca;
use App\Models\PecaServico;
use App\Models\ServicoOrdemDeServico;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Contadores Básicos
        $totalClientes = Cliente::where('deleted', 0)->count();
        $totalVeiculos = Veiculo::where('deleted', 0)->count();
        $totalServicos = Servico::where('deleted', 0)->count();
        $totalPecas = Peca::where('deleted', 0)->count();

        // Consultas com relacionamento
        $servicos = Servico::where('deleted', 0)->orderBy('id', 'asc')->get();
        $pecaServico = PecaServico::where('deleted', 0)
            ->with('peca')
            ->get();
        $ordemServico = OrdemDeServico::where('deleted', 0)
            ->with(['cliente', 'veiculo'])
            ->get();
        $servicoOrdemServico = ServicoOrdemDeServico::where('deleted', 0)
            ->with(['servico', 'ordemdeservico'])
            ->get();

        // Contagem por Status da OS
        $statusCount = OrdemDeServico::where('deleted', 0)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalEmAberto = $statusCount[1] ?? 0;
        $totalEmAndamento = $statusCount[2] ?? 0;
        $totalFinalizados = $statusCount[3] ?? 0;
        $totalCancelados = $statusCount[4] ?? 0;


        // Dados Mensais
        $anoAtual = Carbon::now()->year;
        $mesesDesejados = [6, 7, 8, 9, 10, 11, 12];

        $valoresMensais = OrdemDeServico::where('deleted', 0)
            ->where('status', 3)
            ->whereYear('data_de_saida', $anoAtual)
            ->whereIn(DB::raw('MONTH(data_de_saida)'), $mesesDesejados)
            ->selectRaw('MONTH(data_de_saida) as mes, SUM(valor_total) as total')
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $contagensMensais = OrdemDeServico::where('deleted', 0)
            ->whereYear('data_de_saida', $anoAtual)
            ->whereIn(DB::raw('MONTH(data_de_saida)'), $mesesDesejados)
            ->selectRaw('MONTH(data_de_saida) as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->pluck('total', 'mes');


        // Montagem dos dados graficados
        $nomeMeses = [
            'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];

        $revenueChartData = [];
        $usersChartData = [];

        foreach ($mesesDesejados as $index => $numeroMes) {
            $revenueChartData[] = [
                'label' => $nomeMeses[$index],
                'value' => $valoresMensais[$numeroMes]?? 0
            ];
            $usersChartData[] = [
                'label' => substr($nomeMeses[$index], 0, 3),
                'value' => $contagensMensais[$numeroMes]?? 0
            ];
        }

        // Dados Estáticos
        $stats = $this->getStatsData($totalClientes, $totalVeiculos, $totalServicos, $totalPecas);
        $categoriesData = $this->getCategoriesData($totalEmAberto, $totalEmAndamento, $totalFinalizados, $totalCancelados);
        $topPerformersData = $this->getTopPerformersData();


        // Dados Dinâmicos
        $servicesData = $this->getServicesData($servicos, $pecaServico);
        $recentOrdersData = $this->getRecentOrdersData($ordemServico, $servicoOrdemServico);


        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'topPerformersData' => $topPerformersData,
            'servicesData' => $servicesData,
            'recentOrdersData' => $recentOrdersData,
            'categoriesData' => $categoriesData,
            'revenueChartData' => $revenueChartData,
            'usersChartData' => $usersChartData,
        ]);
    }

    public function getStatsData($totalClientes, $totalVeiculos, $totalServicos, $totalPecas)
    {
         return [
            [
                'title' => 'Total de Clientes',
                'value' => $totalClientes,
                'change' => '+5%',
                'subtitle' => 'últimos 30 dias',
                'variant' => 'success',
            ],
            [
                'title' => 'Total de Veículos',
                'value' => $totalVeiculos,
                'change' => '-10%',
                'subtitle' => 'últimos 30 dias',
                'variant' => 'warning',
            ],
            [
                'title' => 'Total de Serviços',
                'value' => $totalServicos,
                'change' => '+15%',
                'subtitle' => 'últimos 30 dias',
                'variant' => 'success',
            ],
            [
                'title' => 'Total de Peças',
                'value' => $totalPecas,
                'change' => '0%',
                'subtitle' => 'últimos 30 dias',
                'variant' => 'default',
            ],
        ];
    }

    public function getCategoriesData($totalEmAberto, $totalEmAndamento, $totalFinalizados, $totalCancelados)
    {
            return [
            [
                'label' => 'Em Aberto',
                'value' => $totalEmAberto
            ],
            [
                'label' => 'Em Andamento',
                'value' => $totalEmAndamento
            ],
            [
                'label' => 'Finalizados',
                'value' => $totalFinalizados
            ],
            [
                'label' => 'Cancelados',
                'value' => $totalCancelados
            ],

        ];
    }

    public function getTopPerformersData()
    {
       return [
            [
                'avatar' => true,
                'nome' => 'Mecânico Ciro Gomes',
                'especialidade' => 'Motor',
                'trabalhos' => 47,
                'rating' => 4.9,
                'trend' => 12.5
            ],
            [
                'avatar' => true,
                'nome' => 'Eletricista Marina Silva',
                'especialidade' => 'Elétrica',
                'trabalhos' => 42,
                'rating' => 4.8,
                'trend' => 8.3
            ],
        ];
    }

    public function getRecentOrdersData($ordemServico, $servicoOrdemServico)
    {
        $recentOrdersData = [];
        $statusMap = [
            1 => 'em_aberto',
            2 => 'em_andamento',
            3 => 'finalizado',
            4 => 'cancelado'
        ];

        for ($i=0; $i < min(4, count($ordemServico), count($servicoOrdemServico)); $i++) {
            $ordem = $ordemServico[$i];
            $servicoOrdem = $servicoOrdemServico[$i];

            $recentOrdersData[] = [
                'numero' => 'OS-' . $ordem->id,
                'pet' => $ordem->cliente->nome . '/' . $ordem->veiculo->modelo,
                'servico' => $servicoOrdem->servico->nome . ' - ' . $servicoOrdem->servico->descricao,
                'status'  => $statusMap[$ordem->status],
                'total' => $ordem->valor_total,
                'data'  =>'24/11/2024'
            ];
        }

        return $recentOrdersData;
    }

    public function getServicesData($servicos, $pecaServico)
    {
        $servicesData = [];
        for ($i=0; $i < min(4, count($servicos), count($pecaServico)); $i++) {
            $servico = $servicos[$i];
            $peca = $pecaServico[$i];

            $servicesData[] = [
                'nome' => $servico->nome,
                'categoria' => $peca->peca->descricao,
                'preco' => $servico->preco_mao_de_obra,
                'agendamentos' => $peca->peca->quantidade,
                'status' => 'ativo'
            ];
        }
        return $servicesData;
    }
}
