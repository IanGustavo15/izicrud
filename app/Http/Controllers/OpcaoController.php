<?php

namespace App\Http\Controllers;

use App\Models\Opcao;
use Illuminate\Http\Request;

class OpcaoController extends Controller
{
    public function index()
    {
        $items = Opcao::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = Opcao::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('Opcao/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Opcao::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {
        $id_questaoOptions = \App\Models\Questao::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        
        

        return inertia('Opcao/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_questaoOptions' => $id_questaoOptions
            
            
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_questao' => 'required|integer|max:255',
            'letra' => 'required|string|max:255',
            'texto_opcao' => 'required|string|max:255',
        ]);

        Opcao::create($request->all());

        return redirect()->route('opcao.index')->with('success', 'Opcao criado com sucesso.');
    }

        public function edit(Opcao $opcao)
    {
        if ($opcao->deleted) {
            return redirect()->route('opcao.index')->with('error', 'Opcao excluído.');
        }

        $id_questaoOptions = \App\Models\Questao::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        
        

        return inertia('Opcao/create', [
            'item' => $opcao->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_questaoOptions' => $id_questaoOptions
            
            
        ]);
    }


    public function update(Request $request, Opcao $opcao)
    {
        $request->validate([
            'id_questao' => 'required|integer|max:255',
            'letra' => 'required|string|max:255',
            'texto_opcao' => 'required|string|max:255',
        ]);

        $opcao->update($request->all());

        return redirect()->route('opcao.index')->with('success', 'Opcao atualizado com sucesso.');
    }

    public function destroy(Opcao $opcao)
    {
        $opcao->update(['deleted' => 1]);

        return redirect()->route('opcao.index')->with('success', 'Opcao excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Opcaos', 'href' => '/opcao'],
            ['title' => 'Criar Novo Opcao', 'href' => '/opcao/create'],
        ];
    }
}
