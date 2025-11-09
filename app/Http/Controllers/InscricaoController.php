<?php

namespace App\Http\Controllers;

use App\Models\Inscricao;
use Illuminate\Http\Request;

class InscricaoController extends Controller
{
    public function index()
    {
        $items = Inscricao::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = Inscricao::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('Inscricao/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Inscricao::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {
        $id_usersOptions = \App\Models\Users::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        
        

        return inertia('Inscricao/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_usersOptions' => $id_usersOptions
            
            
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_users' => 'required|integer|max:255',
            'data_inscricao' => 'required|date|max:255',
            'status' => 'required|string|max:255',
        ]);

        Inscricao::create($request->all());

        return redirect()->route('inscricao.index')->with('success', 'Inscricao criado com sucesso.');
    }

        public function edit(Inscricao $inscricao)
    {
        if ($inscricao->deleted) {
            return redirect()->route('inscricao.index')->with('error', 'Inscricao excluído.');
        }

        $id_usersOptions = \App\Models\Users::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        
        

        return inertia('Inscricao/create', [
            'item' => $inscricao->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_usersOptions' => $id_usersOptions
            
            
        ]);
    }


    public function update(Request $request, Inscricao $inscricao)
    {
        $request->validate([
            'id_users' => 'required|integer|max:255',
            'data_inscricao' => 'required|date|max:255',
            'status' => 'required|string|max:255',
        ]);

        $inscricao->update($request->all());

        return redirect()->route('inscricao.index')->with('success', 'Inscricao atualizado com sucesso.');
    }

    public function destroy(Inscricao $inscricao)
    {
        $inscricao->update(['deleted' => 1]);

        return redirect()->route('inscricao.index')->with('success', 'Inscricao excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Inscricaos', 'href' => '/inscricao'],
            ['title' => 'Criar Novo Inscricao', 'href' => '/inscricao/create'],
        ];
    }
}
