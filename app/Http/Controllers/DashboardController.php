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

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'topPerformersData' => $topPerformersData,
            'servicesData' => $servicesData,
        ]);
    }
}
