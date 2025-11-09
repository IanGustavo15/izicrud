<?php

namespace App\Http\Controllers;

use App\Models\Resultado;
use Illuminate\Http\Request;

class ResultadoController extends Controller
{
    public function index()
    {
        $items = Resultado::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = Resultado::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('Resultado/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Resultado::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {
        $id_inscricaoOptions = \App\Models\Inscricao::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        
        
        
        
        

        return inertia('Resultado/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_inscricaoOptions' => $id_inscricaoOptions
            
            
            
            
            
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_inscricao' => 'required|integer|max:255',
            'pontuacao_total' => 'required|integer|max:255',
            'acertos' => 'required|integer|max:255',
            'erros' => 'required|integer|max:255',
            'tempo_total_minutos' => 'required|integer|max:255',
            'percentual_acerto' => 'required|numeric|max:255',
        ]);

        Resultado::create($request->all());

        return redirect()->route('resultado.index')->with('success', 'Resultado criado com sucesso.');
    }

        public function edit(Resultado $resultado)
    {
        if ($resultado->deleted) {
            return redirect()->route('resultado.index')->with('error', 'Resultado excluído.');
        }

        $id_inscricaoOptions = \App\Models\Inscricao::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        
        
        
        
        

        return inertia('Resultado/create', [
            'item' => $resultado->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_inscricaoOptions' => $id_inscricaoOptions
            
            
            
            
            
        ]);
    }


    public function update(Request $request, Resultado $resultado)
    {
        $request->validate([
            'id_inscricao' => 'required|integer|max:255',
            'pontuacao_total' => 'required|integer|max:255',
            'acertos' => 'required|integer|max:255',
            'erros' => 'required|integer|max:255',
            'tempo_total_minutos' => 'required|integer|max:255',
            'percentual_acerto' => 'required|numeric|max:255',
        ]);

        $resultado->update($request->all());

        return redirect()->route('resultado.index')->with('success', 'Resultado atualizado com sucesso.');
    }

    public function destroy(Resultado $resultado)
    {
        $resultado->update(['deleted' => 1]);

        return redirect()->route('resultado.index')->with('success', 'Resultado excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Resultados', 'href' => '/resultado'],
            ['title' => 'Criar Novo Resultado', 'href' => '/resultado/create'],
        ];
    }
}
