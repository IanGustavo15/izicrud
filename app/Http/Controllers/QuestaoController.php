<?php

namespace App\Http\Controllers;

use App\Models\Questao;
use Illuminate\Http\Request;

class QuestaoController extends Controller
{
    public function index()
    {
        $items = Questao::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = Questao::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('Questao/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Questao::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {
        
        
        
        
        

        return inertia('Questao/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            
            
            
            
            
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'texto_questao' => 'required|string|max:255',
            'area_concurso' => 'required|string|max:255',
            'diciplina' => 'required|string|max:255',
            'nivel_dificuldade' => 'required|string|max:255',
            'gabarito_correto' => 'required|string|max:255',
        ]);

        Questao::create($request->all());

        return redirect()->route('questao.index')->with('success', 'Questao criado com sucesso.');
    }

        public function edit(Questao $questao)
    {
        if ($questao->deleted) {
            return redirect()->route('questao.index')->with('error', 'Questao excluído.');
        }

        
        
        
        
        

        return inertia('Questao/create', [
            'item' => $questao->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            
            
            
            
            
        ]);
    }


    public function update(Request $request, Questao $questao)
    {
        $request->validate([
            'texto_questao' => 'required|string|max:255',
            'area_concurso' => 'required|string|max:255',
            'diciplina' => 'required|string|max:255',
            'nivel_dificuldade' => 'required|string|max:255',
            'gabarito_correto' => 'required|string|max:255',
        ]);

        $questao->update($request->all());

        return redirect()->route('questao.index')->with('success', 'Questao atualizado com sucesso.');
    }

    public function destroy(Questao $questao)
    {
        $questao->update(['deleted' => 1]);

        return redirect()->route('questao.index')->with('success', 'Questao excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Questaos', 'href' => '/questao'],
            ['title' => 'Criar Novo Questao', 'href' => '/questao/create'],
        ];
    }
}
