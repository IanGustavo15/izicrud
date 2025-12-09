<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\OrdemServico;
use App\Models\Pet;
use App\Models\Servico;
use App\Models\User;
use App\Models\Dono;
use Illuminate\Support\Facades\DB;

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
            'receitaTotal' => $this->receitaTotal(),
            'valorMedioPedido' => $this->valorMedioPedido(),
            'servicosAtivos' => $this->servicosAtivos(),
            'totalDonos' => $this->obterTotalDonos(),
        ]);
    }

    private function obterDadosEstatisticas()
    {
        // Dados reais do sistema veterinário usando Models
        $totalOrdens = OrdemServico::where('deleted', 0)->count();

        // Ordens últimos 30 dias
        $ordensUltimos30Dias = OrdemServico::where('deleted', 0)
            ->where('data_hora', '>=', now()->subDays(30))
            ->count();

        // Ordens 30 dias anteriores (para calcular crescimento)
        $ordens30DiasAnteriores = OrdemServico::where('deleted', 0)
            ->where('data_hora', '>=', now()->subDays(60))
            ->where('data_hora', '<', now()->subDays(30))
            ->count();

        // Crescimento últimos 30 dias
        $crescimento30Dias = $ordens30DiasAnteriores > 0 ? round((($ordensUltimos30Dias - $ordens30DiasAnteriores) / $ordens30DiasAnteriores) * 100, 1) : 0;

        // Total de ordens comparado com período anterior (últimos 30 dias vs 30 dias anteriores)
        $crescimentoTotal = $ordens30DiasAnteriores > 0 ? round((($ordensUltimos30Dias - $ordens30DiasAnteriores) / $ordens30DiasAnteriores) * 100, 1) : 0;

        // Ordens ativas (pendente + em_andamento)
        $ordensAtivas = OrdemServico::where('deleted', 0)
            ->whereIn('status', ['pendente', 'em_andamento'])
            ->count();

        // Ordens ativas há 30 dias
        $ordensAtivas30DiasAtras = OrdemServico::where('deleted', 0)
            ->whereIn('status', ['pendente', 'em_andamento'])
            ->where('data_hora', '>=', now()->subDays(60))
            ->where('data_hora', '<', now()->subDays(30))
            ->count();

        // Crescimento de ordens ativas
        $crescimentoAtivas = $ordensAtivas30DiasAtras > 0 ? round((($ordensAtivas - $ordensAtivas30DiasAtras) / $ordensAtivas30DiasAtras) * 100, 1) : 0;

        return [
            [
                'title' => 'Total de Ordens',
                'value' => number_format($totalOrdens, 0, ',', '.'),
                'change' => ($crescimentoTotal >= 0 ? '+' : '') . $crescimentoTotal . '%',
                'subtitle' => 'todas as ordens',
                'variant' => $crescimentoTotal >= 0 ? 'success' : 'warning',
            ],
            [
                'title' => 'Ordens (30 dias)',
                'value' => $ordensUltimos30Dias,
                'change' => ($crescimento30Dias >= 0 ? '+' : '') . $crescimento30Dias . '%',
                'subtitle' => 'últimos 30 dias',
                'variant' => $crescimento30Dias >= 0 ? 'success' : 'warning',
            ],
            [
                'title' => 'Ordens Ativas',
                'value' => $ordensAtivas,
                'change' => ($crescimentoAtivas >= 0 ? '+' : '') . $crescimentoAtivas . '%',
                'subtitle' => 'pendentes + em andamento',
                'variant' => $crescimentoAtivas >= 0 ? 'success' : 'warning',
            ],
        ];
    }

    private function obterGraficoReceita()
    {
        // Receita real dos últimos 7 meses baseada no valor_total das ordens
        $meses = [];
        for ($i = 6; $i >= 0; $i--) {
            $dataInicio = now()->subMonths($i)->startOfMonth();
            $dataFim = now()->subMonths($i)->endOfMonth();

            // Soma direta do valor_total das ordens de serviço
            $receita = OrdemServico::where('deleted', 0)
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->sum('valor_total');

            $meses[] = [
                'label' => $dataInicio->format('M/y'),
                'value' => (float) $receita,
            ];
        }

        return $meses;
    }

    private function obterGraficoUsuarios()
    {
        // Número de atendimentos (ordens de serviço) por mês nos últimos 7 meses
        $meses = [];
        for ($i = 6; $i >= 0; $i--) {
            $dataInicio = now()->subMonths($i)->startOfMonth();
            $dataFim = now()->subMonths($i)->endOfMonth();

            $atendimentos = OrdemServico::where('deleted', 0)
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->count();

            $meses[] = [
                'label' => $dataInicio->format('M/y'),
                'value' => $atendimentos,
            ];
        }

        return $meses;
    }

    private function obterGraficoCategorias()
    {
        // Distribuição real por espécies de pets usando Model
        $especies = Pet::where('deleted', 0)
            ->select('especie')
            ->selectRaw('count(*) as total')
            ->groupBy('especie')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucfirst($item->especie),
                    'value' => $item->total,
                ];
            });

        // Se não houver dados, retornar dados de exemplo
        if ($especies->isEmpty()) {
            return [['label' => 'Cães', 'value' => 45], ['label' => 'Gatos', 'value' => 30], ['label' => 'Exóticos', 'value' => 8]];
        }

        return $especies->toArray();
    }

    private function obterTabelaPerformers()
    {
        $ordensComDetalhes = OrdemServico::where('deleted', 0)
            ->with('pet:id,nome')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get()
            ->map(function ($ordem) {
                return [
                    'avatar' => true,
                    'numero' => $ordem->numero,
                    'pet' => $ordem->pet?->nome ?: 'Pet não encontrado',
                    'valor' => $ordem->valor_total,
                    'data' => $ordem->created_at->format('d/m/Y'),
                    'id' => $ordem->id,
                ];
            });



        // Estrutura sempre consistente das colunas - teste com larguras diferentes
        $columns = [
            [
                'key' => 'pet',
                'label' => 'Pet',
                'width' => 'xs',  // Mudado para md para evitar conflito
                'align' => 'left',
                'priority' => 1,
            ],
            [
                'key' => 'valor',
                'label' => 'Valor',
                'width' => '100px',  // Largura customizada específica
                'align' => 'right',
                'priority' => 2,
            ],
            [
                'key' => 'data',
                'label' => 'Data',
                'width' => 'sm',
                'align' => 'center',
                'priority' => 4,
            ],
        ];

        return [
            'columns' => $columns,
            'data' => $ordensComDetalhes->toArray(),
        ];
    }

    private function obterTabelaPedidos()
    {
        // Ordens recentes com dados reais usando Models
        $ordensRecentes = OrdemServico::where('deleted', 0)
            ->with(['pet:id,nome,especie'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($ordem) {
                return [
                    'numero' => $ordem->numero,
                    'pet' => $ordem->pet ? $ordem->pet->nome . ' (' . ucfirst($ordem->pet->especie) . ')' : 'Pet não encontrado',
                    'total' => $ordem->valor_total,
                    'data' => $ordem->created_at->format('d/m/Y H:i'),
                    'id' => $ordem->id,
                ];
            });

        // Estrutura sempre consistente das colunas - evitando conflitos de largura
        $columns = [
            ['key' => 'numero', 'label' => '#', 'width' => '60px', 'align' => 'center', 'priority' => 1],
            ['key' => 'pet', 'label' => 'Pet', 'width' => 'lg', 'align' => 'left', 'priority' => 1, 'truncate' => true],
            ['key' => 'total', 'label' => 'Valor', 'width' => '90px', 'align' => 'right', 'priority' => 2]
        ];

        return [
            'columns' => $columns,
            'data' => $ordensRecentes->toArray(),
        ];
    }



    private function receitaTotal()
    {
        // Receita real dos últimos 7 meses
        $receita = DB::table('ordemservicos')
            ->where('deleted', 0)
            ->where('status', 'concluido')
            ->where('data_hora', '>=', now()->subMonths(7))
            ->sum('valor_total');

        return (float) $receita;
    }

    private function valorMedioPedido()
    {
        // Valor médio real das ordens usando Model
        $valorMedio = OrdemServico::where('deleted', 0)->avg('valor_total');

        return (float) $valorMedio ?: 0;
    }

    private function servicosAtivos()
    {
        // Número real de serviços ativos usando Model
        return Servico::where('deleted', 0)->count();
    }

    private function obterTotalDonos()
    {
        return Dono::where('deleted', 0)->count();
    }
}
