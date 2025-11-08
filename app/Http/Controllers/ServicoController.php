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
            'nome' => 'required|string',
            'descricao' => 'required|string',
            'preco_mao_de_obra' => 'required|numeric',
            'tempo_estimado' => 'required|integer',
        ]);

        Servico::create($request->all());

        return redirect()->route('servico.index')->with('success', 'Serviço criado com sucesso.');
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
            'nome' => 'required|string',
            'descricao' => 'required|string',
            'preco_mao_de_obra' => 'required|numeric',
            'tempo_estimado' => 'required|integer',
        ]);

        $servico->update($request->all());

        return redirect()->route('servico.index')->with('success', 'Serviço atualizado com sucesso.');
    }

    public function destroy(Servico $servico)
    {
        $servico->update(['deleted' => 1]);

        return redirect()->route('servico.index')->with('success', 'Serviço excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Serviços', 'href' => '/servico'],
            ['title' => 'Criar Novo Serviço', 'href' => '/servico/create'],
        ];
    }
}
