<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
use Illuminate\Http\Request;

class VeiculoController extends Controller
{
    public function index()
    {
        $items = Veiculo::where('deleted', 0)->with('cliente')->orderBy('id', 'desc')->paginate(9);
        $allItems = Veiculo::where('deleted', 0)->with('cliente')->orderBy('id', 'desc')->get();


        // dd($items);
        return inertia('Veiculo/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Veiculo::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {
        $id_clienteOptions = \App\Models\Cliente::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome,
                     // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });




        return inertia('Veiculo/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_clienteOptions' => $id_clienteOptions




        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_cliente' => 'required|integer',
            'placa' => 'required|string|max:7',
            'modelo' => 'required|string',
            'ano' => 'required|integer',
            'tipo' => 'required|integer|max:2',
        ]);
        // dd($request);

        Veiculo::create($request->all());

        return redirect()->route('veiculo.index')->with('success', 'Veículo criado com sucesso.');
    }

        public function edit(Veiculo $veiculo)
    {
        if ($veiculo->deleted) {
            return redirect()->route('veiculo.index')->with('error', 'Veículo excluído.');
        }

        $id_clienteOptions = \App\Models\Cliente::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });





        return inertia('Veiculo/create', [
            'item' => $veiculo->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_clienteOptions' => $id_clienteOptions




        ]);
    }


    public function update(Request $request, Veiculo $veiculo)
    {
        $request->validate([
            'id_cliente' => 'required|integer',
            'placa' => 'required|string|max:7',
            'modelo' => 'required|string',
            'ano' => 'required|integer',
            'tipo' => 'required|integer|max:2',
        ]);

        $veiculo->update($request->all());

        return redirect()->route('veiculo.index')->with('success', 'Veículo atualizado com sucesso.');
    }

    public function destroy(Veiculo $veiculo)
    {
        $veiculo->update(['deleted' => 1]);

        return redirect()->route('veiculo.index')->with('success', 'Veículo excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Veiculos', 'href' => '/veiculo'],
            ['title' => 'Criar Novo Veiculo', 'href' => '/veiculo/create'],
            ['title' => 'Todos as Ordens de Serviço', 'href' => '/ordemdeservico'],
        ];
    }
}
