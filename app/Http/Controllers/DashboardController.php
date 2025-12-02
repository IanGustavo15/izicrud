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
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClientes = Cliente::where('deleted', 0)->orderBy('id', 'desc')->count();
        $totalVeiculos = Veiculo::where('deleted', 0)->orderBy('id', 'desc')->count();
        $totalServicos = Servico::where('deleted', 0)->orderBy('id', 'desc')->count();
        $totalPecas = Peca::where('deleted', 0)->orderBy('id', 'desc')->count();
        $servicos = Servico::where('deleted', 0)->orderBy('id', 'asc')->get();
        $pecaServico = PecaServico::where('deleted', 0)->orderBy('id', 'asc')->get();
        $ordemServico = OrdemDeServico::where('deleted', 0)->with('cliente')->with('veiculo')->orderBy('id', 'asc')->get();
        $servicoOrdemServico = ServicoOrdemDeServico::where('deleted', 0)->with('servico')->with('ordemdeservico')->orderBy('id', 'asc')->get();

        // Utilizados no gráfico de Pizza/Donut
        $totalEmAberto = OrdemDeServico::where('deleted', 0)->where('status', 1)->count();
        $totalEmAndamento = OrdemDeServico::where('deleted', 0)->where('status', 2)->count();
        $totalFinalizados = OrdemDeServico::where('deleted', 0)->where('status', 3)->count(); // Utilizado nas duas formas
        $totalCancelados = OrdemDeServico::where('deleted', 0)->where('status', 4)->count();

        $totalOrdemServico = OrdemDeServico::where('deleted', 0)->where('status', 1)->count() + OrdemDeServico::where('deleted', 0)->where('status', 2)->count() + OrdemDeServico::where('deleted', 0)->where('status', 4)->count(); // Todas que não estão finalizadas
        // dd($totalFinalizados);
        // dd($totalOrdemServico);

        // Utilizados no gráfico de Linhas
        $valorJun = 0;
        $valorJul = 0;
        $valorAgo = 0;
        $valorSet = 0;
        $valorOut = 0;
        $valorNov = 0;
        $valorDez = 0;


        $junOrdemServico = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 6)->whereYear('data_de_saida', 2025)->get();
        foreach ($junOrdemServico as $serv) {
            $valorJun = $valorJun + $serv->valor_total;
        };
        // dd($valorJun);
        $junOrdemServicoCount = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 6)->whereYear('data_de_saida', 2025)->count();


        $julOrdemServico = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 7)->whereYear('data_de_saida', 2025)->get();
        foreach ($julOrdemServico as $serv) {
            $valorJul = $valorJul + $serv->valor_total;
        };
        $julOrdemServicoCount = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 7)->whereYear('data_de_saida', 2025)->count();


        $agoOrdemServico = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 8)->whereYear('data_de_saida', 2025)->get();
        foreach ($agoOrdemServico as $serv) {
            $valorAgo = $valorAgo + $serv->valor_total;
        };
        $agoOrdemServicoCount = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 8)->whereYear('data_de_saida', 2025)->count();



        $setOrdemServico = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 9)->whereYear('data_de_saida', 2025)->get();
        foreach ($setOrdemServico as $serv) {
            $valorSet = $valorSet + $serv->valor_total;
        };
        // dd($valorSet);
        $setOrdemServicoCount = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 9)->whereYear('data_de_saida', 2025)->count();


        $outOrdemServico = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 10)->whereYear('data_de_saida', 2025)->get();
        foreach ($outOrdemServico as $serv) {
            $valorOut = $valorOut + $serv->valor_total;
        };
        $outOrdemServicoCount = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 10)->whereYear('data_de_saida', 2025)->count();


        $novOrdemServico = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 11)->whereYear('data_de_saida', 2025)->get();
        foreach ($novOrdemServico as $serv) {
            $valorNov = $valorNov + $serv->valor_total;
        };
        $novOrdemServicoCount = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 11)->whereYear('data_de_saida', 2025)->count();


        $dezOrdemServico = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 12)->whereYear('data_de_saida', 2025)->get();
        foreach ($dezOrdemServico as $serv) {
            $valorDez = $valorDez + $serv->valor_total;
        };
        $dezOrdemServicoCount = DB::table('ordemdeservicos')->where('deleted', 0)->where('status', 3)->whereMonth('data_de_saida', 12)->whereYear('data_de_saida', 2025)->count();


        // dd($junOrdemServico);
        // dd($junOrdemServicoCount);

        // dd($julOrdemServico);
        // dd($julOrdemServicoCount);

        // dd($agoOrdemServico);
        // dd($agoOrdemServicoCount);

        // dd($setOrdemServico);
        // dd($setOrdemServicoCount);

        // dd($outOrdemServico);
        // dd($outOrdemServicoCount);

        // dd($novOrdemServico);
        // dd($novOrdemServicoCount);

        // dd($dezOrdemServico);
        // dd($dezOrdemServicoCount);

        // dd($pecaServico);
        // dd($servicos);

        $stats = [
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

        // $categoriesData = [ // Forma de Finalizados + Outros
        //     [
        //         'label' => 'Finalizados',
        //         'value' => $totalFinalizados
        //     ],
        //     [
        //         'label' => 'Outros',
        //         'value' => $totalOrdemServico
        //     ],
        // ];

        $categoriesData = [ // Forma com todos os Status
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

        $topPerformersData = [
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

        $servicesData = [
            [
                'nome' => $servicos[0]->nome,
                'categoria' => $pecaServico[0]->peca->descricao,
                'preco' => $servicos[0]->preco_mao_de_obra,
                'agendamentos' => $pecaServico[0]->peca->quantidade,
                'status' => 'ativo'
            ],
            [
                'nome' => $servicos[1]->nome,
                'categoria' => $pecaServico[1]->peca->descricao,
                'preco' => $servicos[1]->preco_mao_de_obra,
                'agendamentos' => $pecaServico[1]->peca->quantidade,
                'status' => 'ativo'
            ],
            [
                'nome' => $servicos[2]->nome,
                'categoria' => $pecaServico[2]->peca->descricao,
                'preco' => $servicos[2]->preco_mao_de_obra,
                'agendamentos' => $pecaServico[2]->peca->quantidade,
                'status' => 'ativo'
            ],
            [
                'nome' => $servicos[3]->nome,
                'categoria' => $pecaServico[3]->peca->descricao  . ' / ' . $pecaServico[4]->peca->descricao,
                'preco' => $servicos[3]->preco_mao_de_obra,
                'agendamentos' => $pecaServico[3]->peca->quantidade . ' / ' . $pecaServico[4]->peca->quantidade,
                'status' => 'ativo'
            ],
        ];
        // dd($ordemServico);

        foreach ($ordemServico as $os) {
            if ($os->status == 1) {
            $os->status = 'em_aberto';
        } elseif ($os->status == 2) {
            $os->status = 'em_andamento';
        } elseif ($os->status == 3) {
            $os->status = 'finalizado';
        } else {
            $os->status = 'cancelado';
        }
        // dd($os);
        }

        // dd($servicoOrdemServico);

        $recentOrdersData = [
            [
                'numero' => 'OS-' . $ordemServico[0]->id,
                'pet' => $ordemServico[0]->cliente->nome . '/' . $ordemServico[0]->veiculo->modelo,
                'servico' => $servicoOrdemServico[0]->servico->nome . ' - ' . $servicoOrdemServico[0]->servico->descricao,
                'status'  => $ordemServico[0]->status,
                'total' => $ordemServico[0]->valor_total,
                'data'  =>'24/11/2024'
            ],
            [
                'numero' => 'OS-' . $ordemServico[1]->id,
                'pet' => $ordemServico[1]->cliente->nome . '/' . $ordemServico[1]->veiculo->modelo,
                'servico' => $servicoOrdemServico[1]->servico->nome . ' - ' . $servicoOrdemServico[1]->servico->descricao,
                'status'  => $ordemServico[1]->status,
                'total' => $ordemServico[1]->valor_total,
                'data'  =>'24/11/2024'
            ],
            [
                'numero' => 'OS-' . $ordemServico[2]->id,
                'pet' => $ordemServico[2]->cliente->nome . '/' . $ordemServico[2]->veiculo->modelo,
                'servico' => $servicoOrdemServico[2]->servico->nome . ' - ' . $servicoOrdemServico[2]->servico->descricao,
                'status'  => $ordemServico[2]->status,
                'total' => $ordemServico[2]->valor_total,
                'data'  =>'24/11/2024'
            ],
            [
                'numero' => 'OS-' . $ordemServico[3]->id,
                'pet' => $ordemServico[3]->cliente->nome . '/' . $ordemServico[3]->veiculo->modelo,
                'servico' => $servicoOrdemServico[3]->servico->nome . ' - ' . $servicoOrdemServico[3]->servico->descricao,
                'status'  => $ordemServico[3]->status,
                'total' => $ordemServico[3]->valor_total,
                'data'  =>'24/11/2024'
            ],
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'topPerformersData' => $topPerformersData,
            'servicesData' => $servicesData,
            'recentOrdersData' => $recentOrdersData,
            'categoriesData' => $categoriesData,
        ]);
    }
}
