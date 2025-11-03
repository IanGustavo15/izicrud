<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use Illuminate\Http\Request;

class OrdemServicoController extends Controller
{
    public function index()
    {
        $items = OrdemServico::where('deleted', 0)
        ->with('moto')
        ->with('servico')
        ->orderBy('id', 'desc')->paginate(9);
        $allItems = OrdemServico::where('deleted', 0)
        ->with('moto')
        ->with('servico')
        ->orderBy('id', 'desc')->get();

        // dd($items);

        return inertia('OrdemServico/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => OrdemServico::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }



        public function create()
    {
        $id_servicoOptions = \App\Models\Servico::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->descricao .' - R$ '. $item->valor // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        $id_motoOptions = \App\Models\Moto::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->modelo // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });



        return inertia('OrdemServico/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_servicoOptions' => $id_servicoOptions
            ,'id_motoOptions' => $id_motoOptions


        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_servico' => 'required|integer|max:255',
            'id_moto' => 'required|integer|max:255',
            'data_servico' => 'required|date|max:255',
            'realizado' => 'required|boolean|max:255',
        ]);

        OrdemServico::create($request->all());

        return redirect()->route('ordemservico.index')->with('success', 'OrdemServico criado com sucesso.');
    }

        public function edit(OrdemServico $ordemservico)
    {
        if ($ordemservico->deleted) {
            return redirect()->route('ordemservico.index')->with('error', 'OrdemServico excluído.');
        }

        $id_servicoOptions = \App\Models\Servico::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->descricao .' - R$ '. $item->valor // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        $id_motoOptions = \App\Models\Moto::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->modelo // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });



        return inertia('OrdemServico/create', [
            'item' => $ordemservico->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_servicoOptions' => $id_servicoOptions
            ,'id_motoOptions' => $id_motoOptions


        ]);
    }


    public function update(Request $request, OrdemServico $ordemservico)
    {
        $request->validate([
            'id_servico' => 'required|integer|max:255',
            'id_moto' => 'required|integer|max:255',
            'data_servico' => 'required|date|max:255',
            'realizado' => 'required|boolean|max:255',
        ]);

        $ordemservico->update($request->all());

        return redirect()->route('ordemservico.index')->with('success', 'OrdemServico atualizado com sucesso.');
    }

    public function destroy(OrdemServico $ordemservico)
    {
        $ordemservico->update(['deleted' => 1]);

        return redirect()->route('ordemservico.index')->with('success', 'OrdemServico excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os OrdemServicos', 'href' => '/ordemservico'],
            ['title' => 'Criar Novo OrdemServico', 'href' => '/ordemservico/create'],
        ];
    }
}
