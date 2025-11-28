<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClientes = \App\Models\Cliente::where('deleted', 0)->orderBy('id', 'desc')->count();
        $totalVeiculos = \App\Models\Veiculo::where('deleted', 0)->orderBy('id', 'desc')->count();
        $totalServicos = \App\Models\Servico::where('deleted', 0)->orderBy('id', 'desc')->count();
        $totalPecas = \App\Models\Peca::where('deleted', 0)->orderBy('id', 'desc')->count();
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

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}
