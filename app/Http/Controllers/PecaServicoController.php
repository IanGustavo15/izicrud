<?php

namespace App\Http\Controllers;

use App\Models\PecaServico;
use Illuminate\Http\Request;

class PecaServicoController extends Controller
{
    public function index()
    {
        $items = PecaServico::where('deleted', 0)->with('servico')->with('peca')->orderBy('id', 'desc')->paginate(9);
        $allItems = PecaServico::where('deleted', 0)->with('servico')->with('peca')->orderBy('id', 'desc')->get();

        return inertia('PecaServico/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => PecaServico::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {
        $id_servicoOptions = \App\Models\Servico::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome,
                    'descricao' => $item->descricao, // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        $id_pecaOptions = \App\Models\Peca::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->descricao,
                    'quantidade' => $item->quantidade, // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });


        return inertia('PecaServico/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_servicoOptions' => $id_servicoOptions
            ,'id_pecaOptions' => $id_pecaOptions

        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_servico' => 'required|integer',
            'id_peca' => 'required|integer',
            'quantidade_peca' => 'required|integer',
        ]);

        PecaServico::create($request->all());

        return redirect()->route('pecaservico.index')->with('success', 'PecaServico criado com sucesso.');
    }

        public function edit(PecaServico $pecaservico)
    {
        if ($pecaservico->deleted) {
            return redirect()->route('pecaservico.index')->with('error', 'PecaServico excluído.');
        }

        $id_servicoOptions = \App\Models\Servico::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        $id_pecaOptions = \App\Models\Peca::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->descricao,
                    'quantidade' => $item->quantidade, // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });


        return inertia('PecaServico/create', [
            'item' => $pecaservico->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_servicoOptions' => $id_servicoOptions
            ,'id_pecaOptions' => $id_pecaOptions

        ]);
    }


    public function update(Request $request, PecaServico $pecaservico)
    {
        $request->validate([
            'id_servico' => 'required|integer',
            'id_peca' => 'required|integer',
            'quantidade_peca' => 'required|integer',
        ]);

        $pecaservico->update($request->all());

        return redirect()->route('pecaservico.index')->with('success', 'PecaServico atualizado com sucesso.');
    }

    public function destroy(PecaServico $pecaservico)
    {
        $pecaservico->update(['deleted' => 1]);

        return redirect()->route('pecaservico.index')->with('success', 'PecaServico excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os PecaServicos', 'href' => '/pecaservico'],
            ['title' => 'Criar Novo PecaServico', 'href' => '/pecaservico/create'],
        ];
    }
}
