<?php

namespace App\Http\Controllers;

use App\Models\Metrica;
use Illuminate\Http\Request;

class MetricaController extends Controller
{
    public function index()
    {
        $items = Metrica::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = Metrica::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('Metrica/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Metrica::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {
        $id_simuladoOptions = \App\Models\Simulado::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        
        

        return inertia('Metrica/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_simuladoOptions' => $id_simuladoOptions
            
            
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_simulado' => 'required|integer|max:255',
            'media_geral_pontuacao' => 'required|numeric|max:255',
            'base_vagas' => 'required|integer|max:255',
        ]);

        Metrica::create($request->all());

        return redirect()->route('metrica.index')->with('success', 'Metrica criado com sucesso.');
    }

        public function edit(Metrica $metrica)
    {
        if ($metrica->deleted) {
            return redirect()->route('metrica.index')->with('error', 'Metrica excluído.');
        }

        $id_simuladoOptions = \App\Models\Simulado::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        
        

        return inertia('Metrica/create', [
            'item' => $metrica->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_simuladoOptions' => $id_simuladoOptions
            
            
        ]);
    }


    public function update(Request $request, Metrica $metrica)
    {
        $request->validate([
            'id_simulado' => 'required|integer|max:255',
            'media_geral_pontuacao' => 'required|numeric|max:255',
            'base_vagas' => 'required|integer|max:255',
        ]);

        $metrica->update($request->all());

        return redirect()->route('metrica.index')->with('success', 'Metrica atualizado com sucesso.');
    }

    public function destroy(Metrica $metrica)
    {
        $metrica->update(['deleted' => 1]);

        return redirect()->route('metrica.index')->with('success', 'Metrica excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Metricas', 'href' => '/metrica'],
            ['title' => 'Criar Novo Metrica', 'href' => '/metrica/create'],
        ];
    }
}
