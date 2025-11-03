<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use Illuminate\Http\Request;

class ServicoController extends Controller
{
    public function index()
    {
        $items = Servico::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = Servico::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('Servico/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Servico::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {
        
        

        return inertia('Servico/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            
            
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|max:255',
        ]);

        Servico::create($request->all());

        return redirect()->route('servico.index')->with('success', 'Servico criado com sucesso.');
    }

        public function edit(Servico $servico)
    {
        if ($servico->deleted) {
            return redirect()->route('servico.index')->with('error', 'Servico excluído.');
        }

        
        

        return inertia('Servico/create', [
            'item' => $servico->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            
            
        ]);
    }


    public function update(Request $request, Servico $servico)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|max:255',
        ]);

        $servico->update($request->all());

        return redirect()->route('servico.index')->with('success', 'Servico atualizado com sucesso.');
    }

    public function destroy(Servico $servico)
    {
        $servico->update(['deleted' => 1]);

        return redirect()->route('servico.index')->with('success', 'Servico excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Servicos', 'href' => '/servico'],
            ['title' => 'Criar Novo Servico', 'href' => '/servico/create'],
        ];
    }
}
