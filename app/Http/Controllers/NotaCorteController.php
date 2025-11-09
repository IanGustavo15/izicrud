<?php

namespace App\Http\Controllers;

use App\Models\NotaCorte;
use Illuminate\Http\Request;

class NotaCorteController extends Controller
{
    public function index()
    {
        $items = NotaCorte::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = NotaCorte::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('NotaCorte/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => NotaCorte::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {
        $id_simuladoOptions = \App\Models\Simulado::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        

        return inertia('NotaCorte/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_simuladoOptions' => $id_simuladoOptions
            
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_simulado' => 'required|integer|max:255',
            'valor_corte' => 'required|integer|max:255',
        ]);

        NotaCorte::create($request->all());

        return redirect()->route('notacorte.index')->with('success', 'NotaCorte criado com sucesso.');
    }

        public function edit(NotaCorte $notacorte)
    {
        if ($notacorte->deleted) {
            return redirect()->route('notacorte.index')->with('error', 'NotaCorte excluído.');
        }

        $id_simuladoOptions = \App\Models\Simulado::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        

        return inertia('NotaCorte/create', [
            'item' => $notacorte->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_simuladoOptions' => $id_simuladoOptions
            
        ]);
    }


    public function update(Request $request, NotaCorte $notacorte)
    {
        $request->validate([
            'id_simulado' => 'required|integer|max:255',
            'valor_corte' => 'required|integer|max:255',
        ]);

        $notacorte->update($request->all());

        return redirect()->route('notacorte.index')->with('success', 'NotaCorte atualizado com sucesso.');
    }

    public function destroy(NotaCorte $notacorte)
    {
        $notacorte->update(['deleted' => 1]);

        return redirect()->route('notacorte.index')->with('success', 'NotaCorte excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os NotaCortes', 'href' => '/notacorte'],
            ['title' => 'Criar Novo NotaCorte', 'href' => '/notacorte/create'],
        ];
    }
}
