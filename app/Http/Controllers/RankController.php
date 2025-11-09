<?php

namespace App\Http\Controllers;

use App\Models\Rank;
use Illuminate\Http\Request;

class RankController extends Controller
{
    public function index()
    {
        $items = Rank::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = Rank::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('Rank/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Rank::where('deleted', 1)->count(),
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
        $id_usersOptions = \App\Models\Users::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        
        
        

        return inertia('Rank/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_simuladoOptions' => $id_simuladoOptions
            ,'id_usersOptions' => $id_usersOptions
            
            
            
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_simulado' => 'required|integer|max:255',
            'id_users' => 'required|integer|max:255',
            'pontuacao_final' => 'required|integer|max:255',
            'posicao_rank' => 'required|integer|max:255',
            'classificacao' => 'required|boolean|max:255',
        ]);

        Rank::create($request->all());

        return redirect()->route('rank.index')->with('success', 'Rank criado com sucesso.');
    }

        public function edit(Rank $rank)
    {
        if ($rank->deleted) {
            return redirect()->route('rank.index')->with('error', 'Rank excluído.');
        }

        $id_simuladoOptions = \App\Models\Simulado::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        $id_usersOptions = \App\Models\Users::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        
        
        

        return inertia('Rank/create', [
            'item' => $rank->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_simuladoOptions' => $id_simuladoOptions
            ,'id_usersOptions' => $id_usersOptions
            
            
            
        ]);
    }


    public function update(Request $request, Rank $rank)
    {
        $request->validate([
            'id_simulado' => 'required|integer|max:255',
            'id_users' => 'required|integer|max:255',
            'pontuacao_final' => 'required|integer|max:255',
            'posicao_rank' => 'required|integer|max:255',
            'classificacao' => 'required|boolean|max:255',
        ]);

        $rank->update($request->all());

        return redirect()->route('rank.index')->with('success', 'Rank atualizado com sucesso.');
    }

    public function destroy(Rank $rank)
    {
        $rank->update(['deleted' => 1]);

        return redirect()->route('rank.index')->with('success', 'Rank excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Ranks', 'href' => '/rank'],
            ['title' => 'Criar Novo Rank', 'href' => '/rank/create'],
        ];
    }
}
