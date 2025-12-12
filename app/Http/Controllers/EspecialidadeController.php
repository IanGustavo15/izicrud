<?php

namespace App\Http\Controllers;

use App\Models\Especialidade;
use Illuminate\Http\Request;

class EspecialidadeController extends Controller
{
    public function index()
    {
        $items = Especialidade::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = Especialidade::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('Especialidade/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Especialidade::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {


        return inertia('Especialidade/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()

        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'especialidade' => 'required|string|max:255',
        ]);

        Especialidade::create($request->all());

        return redirect()->route('especialidade.index')->with('success', 'Especialidade criado com sucesso.');
    }

        public function edit(Especialidade $especialidade)
    {
        if ($especialidade->deleted) {
            return redirect()->route('especialidade.index')->with('error', 'Especialidade excluído.');
        }



        return inertia('Especialidade/create', [
            'item' => $especialidade->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()

        ]);
    }


    public function update(Request $request, Especialidade $especialidade)
    {
        $request->validate([
            'especialidade' => 'required|string|max:255',
        ]);

        $especialidade->update($request->all());

        return redirect()->route('especialidade.index')->with('success', 'Especialidade atualizado com sucesso.');
    }

    public function destroy(Especialidade $especialidade)
    {
        $especialidade->update(['deleted' => 1]);

        return redirect()->route('especialidade.index')->with('success', 'Especialidade excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Especialidades', 'href' => '/especialidade'],
            ['title' => 'Criar Novo Especialidade', 'href' => '/especialidade/create'],
            ['title' => 'Todos os Trabalhadores', 'href' => '/trabalhador'],
            ['title' => 'Criar Novo Trabalhador', 'href' => '/trabalhador/create'],
        ];
    }
}
