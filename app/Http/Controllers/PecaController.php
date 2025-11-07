<?php

namespace App\Http\Controllers;

use App\Models\Peca;
use Illuminate\Http\Request;

class PecaController extends Controller
{
    public function index()
    {
        $items = Peca::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = Peca::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('Peca/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Peca::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {







        return inertia('Peca/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()






        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string',
            'codigo_unico' => 'required|string|max:255',
            'preco_de_custo' => 'required|numeric',
            'preco_de_venda' => 'required|numeric',
            'quantidade' => 'required|integer',
            'estoque' => 'required|integer',
        ]);

        Peca::create($request->all());

        return redirect()->route('peca.index')->with('success', 'Peça criada com sucesso.');
    }

        public function edit(Peca $peca)
    {
        if ($peca->deleted) {
            return redirect()->route('peca.index')->with('error', 'Peça excluída.');
        }








        return inertia('Peca/create', [
            'item' => $peca->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()






        ]);
    }


    public function update(Request $request, Peca $peca)
    {
        $request->validate([
            'descricao' => 'required|string',
            'codigo_unico' => 'required|string|max:255',
            'preco_de_custo' => 'required|numeric',
            'preco_de_venda' => 'required|numeric',
            'quantidade' => 'required|integer',
            'estoque' => 'required|integer',
        ]);

        $peca->update($request->all());

        return redirect()->route('peca.index')->with('success', 'Peça atualizada com sucesso.');
    }

    public function destroy(Peca $peca)
    {
        $peca->update(['deleted' => 1]);

        return redirect()->route('peca.index')->with('success', 'Peça excluída com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos as Peças', 'href' => '/peca'],
            ['title' => 'Criar Nova Peça', 'href' => '/peca/create'],
        ];
    }
}
