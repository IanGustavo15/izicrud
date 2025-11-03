<?php

namespace App\Http\Controllers;

use App\Models\Moto;
use Illuminate\Http\Request;

class MotoController extends Controller
{
    public function index()
    {
        $items = Moto::where('deleted', 0)
            ->with('cliente')
            ->orderBy('id', 'desc')->paginate(9);
        $allItems = Moto::where('deleted', 0)
            ->with('cliente')
            ->orderBy('id', 'desc')->get();

        return inertia('Moto/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Moto::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {


        $id_clienteOptions = \App\Models\Cliente::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });

        return inertia('Moto/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()


            ,'id_clienteOptions' => $id_clienteOptions
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'modelo' => 'required|string|max:255',
            'placa' => 'required|string|max:255',
            'id_cliente' => 'required|integer|max:255',
        ]);

        Moto::create($request->all());

        return redirect()->route('moto.index')->with('success', 'Moto criado com sucesso.');
    }

        public function edit(Moto $moto)
    {
        if ($moto->deleted) {
            return redirect()->route('moto.index')->with('error', 'Moto excluído.');
        }



        $id_clienteOptions = \App\Models\Cliente::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });

        return inertia('Moto/create', [
            'item' => $moto->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()


            ,'id_clienteOptions' => $id_clienteOptions
        ]);
    }


    public function update(Request $request, Moto $moto)
    {
        $request->validate([
            'modelo' => 'required|string|max:255',
            'placa' => 'required|string|max:255',
            'id_cliente' => 'required|integer|max:255',
        ]);

        $moto->update($request->all());

        return redirect()->route('moto.index')->with('success', 'Moto atualizado com sucesso.');
    }

    public function destroy(Moto $moto)
    {
        $moto->update(['deleted' => 1]);

        return redirect()->route('moto.index')->with('success', 'Moto excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Motos', 'href' => '/moto'],
            ['title' => 'Criar Novo Moto', 'href' => '/moto/create'],
        ];
    }
}
