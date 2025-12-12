<?php

namespace App\Http\Controllers;

use App\Models\Trabalhador;
use Illuminate\Http\Request;

class TrabalhadorController extends Controller
{
    public function index()
    {
        $items = Trabalhador::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = Trabalhador::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('Trabalhador/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Trabalhador::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {

        $especialidade = Trabalhador::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->especialidade // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
            // dd($especialidade);






        return inertia('Trabalhador/create', [
            'sidebarNavItems' => $this->getSidebarNavItems(),
            'especialidade' => $especialidade,





        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'especialidade' => 'required|integer|max:255',
            'valorHora' => 'required|numeric',
            'status' => 'required|integer|max:255',
            'qualidade' => 'required|numeric',
        ]);

        Trabalhador::create($request->all());

        return redirect()->route('trabalhador.index')->with('success', 'Trabalhador criado com sucesso.');
    }

        public function edit(Trabalhador $trabalhador)
    {
        if ($trabalhador->deleted) {
            return redirect()->route('trabalhador.index')->with('error', 'Trabalhador excluído.');
        }







        return inertia('Trabalhador/create', [
            'item' => $trabalhador->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()





        ]);
    }


    public function update(Request $request, Trabalhador $trabalhador)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'especialidade' => 'required|integer|max:255',
            'valorHora' => 'required|numeric',
            'status' => 'required|integer|max:255',
            'qualidade' => 'required|numeric',
        ]);

        $trabalhador->update($request->all());

        return redirect()->route('trabalhador.index')->with('success', 'Trabalhador atualizado com sucesso.');
    }

    public function destroy(Trabalhador $trabalhador)
    {
        $trabalhador->update(['deleted' => 1]);

        return redirect()->route('trabalhador.index')->with('success', 'Trabalhador excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Trabalhadores', 'href' => '/trabalhador'],
            ['title' => 'Criar Novo Trabalhador', 'href' => '/trabalhador/create'],
            ['title' => 'Todos as Especialidades', 'href' => '/especialidade'],
            ['title' => 'Criar Nova Especialidade', 'href' => '/especialidade/create'],
        ];
    }
}
