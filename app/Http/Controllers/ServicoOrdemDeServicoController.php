<?php

namespace App\Http\Controllers;

use App\Models\ServicoOrdemDeServico;
use Illuminate\Http\Request;

class ServicoOrdemDeServicoController extends Controller
{
    public function index()
    {
        $items = ServicoOrdemDeServico::where('deleted', 0)->with('servico')->with('ordemdeservico')->orderBy('id', 'desc')->paginate(9);
        $allItems = ServicoOrdemDeServico::where('deleted', 0)->with('servico')->with('ordemdeservico')->orderBy('id', 'desc')->get();

        return inertia('ServicoOrdemDeServico/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => ServicoOrdemDeServico::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {
        $id_ordemdeservicoOptions = \App\Models\Ordemdeservico::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->cliente->nome,
                    'veiculo' => $item->veiculo->modelo,
                     // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        $id_servicoOptions = \App\Models\Servico::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome . ' - ' . $item->descricao, // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });



        return inertia('ServicoOrdemDeServico/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_ordemdeservicoOptions' => $id_ordemdeservicoOptions
            ,'id_servicoOptions' => $id_servicoOptions


        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_ordemdeservico' => 'integer',
            'id_servico' => 'integer',
            'quantidade' => 'integer',
            'preco_unitario' => 'numeric',
        ]);

        ServicoOrdemDeServico::create($request->all());

        return redirect()->route('servicoordemdeservico.index')->with('success', 'ServicoOrdemDeServico criado com sucesso.');
    }

        public function edit(ServicoOrdemDeServico $servicoordemdeservico)
    {
        if ($servicoordemdeservico->deleted) {
            return redirect()->route('servicoordemdeservico.index')->with('error', 'ServicoOrdemDeServico excluído.');
        }

        $id_ordemdeservicoOptions = \App\Models\Ordemdeservico::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->cliente->nome,
                    'veiculo' => $item->veiculo->modelo,
                     // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        $id_servicoOptions = \App\Models\Servico::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome . ' - ' . $item->descricao, // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });



        return inertia('ServicoOrdemDeServico/create', [
            'item' => $servicoordemdeservico->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_ordemdeservicoOptions' => $id_ordemdeservicoOptions
            ,'id_servicoOptions' => $id_servicoOptions


        ]);
    }


    public function update(Request $request, ServicoOrdemDeServico $servicoordemdeservico)
    {
        $request->validate([
            'id_ordemdeservico' => 'integer',
            'id_servico' => 'integer',
            'quantidade' => 'integer',
            'preco_unitario' => 'numeric',
        ]);

        $servicoordemdeservico->update($request->all());

        return redirect()->route('servicoordemdeservico.index')->with('success', 'ServicoOrdemDeServico atualizado com sucesso.');
    }

    public function destroy(ServicoOrdemDeServico $servicoordemdeservico)
    {
        $servicoordemdeservico->update(['deleted' => 1]);

        return redirect()->route('servicoordemdeservico.index')->with('success', 'ServicoOrdemDeServico excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os ServicoOrdemDeServicos', 'href' => '/servicoordemdeservico'],
            ['title' => 'Criar Novo ServicoOrdemDeServico', 'href' => '/servicoordemdeservico/create'],
        ];
    }
}
