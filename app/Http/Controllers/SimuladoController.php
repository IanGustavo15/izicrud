<?php

namespace App\Http\Controllers;

use App\Models\Simulado;
use Illuminate\Http\Request;

class SimuladoController extends Controller
{
    public function index()
    {
        $items = Simulado::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = Simulado::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('Simulado/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Simulado::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {
        
        
        
        
        
        

        return inertia('Simulado/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            
            
            
            
            
            
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string|max:255',
            'data_inicio' => 'required|date|max:255',
            'data_fim' => 'required|date|max:255',
            'duracao_minutos' => 'required|integer|max:255',
            'numero_vagas' => 'required|integer|max:255',
        ]);

        Simulado::create($request->all());

        return redirect()->route('simulado.index')->with('success', 'Simulado criado com sucesso.');
    }

        public function edit(Simulado $simulado)
    {
        if ($simulado->deleted) {
            return redirect()->route('simulado.index')->with('error', 'Simulado excluído.');
        }

        
        
        
        
        
        

        return inertia('Simulado/create', [
            'item' => $simulado->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            
            
            
            
            
            
        ]);
    }


    public function update(Request $request, Simulado $simulado)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string|max:255',
            'data_inicio' => 'required|date|max:255',
            'data_fim' => 'required|date|max:255',
            'duracao_minutos' => 'required|integer|max:255',
            'numero_vagas' => 'required|integer|max:255',
        ]);

        $simulado->update($request->all());

        return redirect()->route('simulado.index')->with('success', 'Simulado atualizado com sucesso.');
    }

    public function destroy(Simulado $simulado)
    {
        $simulado->update(['deleted' => 1]);

        return redirect()->route('simulado.index')->with('success', 'Simulado excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Simulados', 'href' => '/simulado'],
            ['title' => 'Criar Novo Simulado', 'href' => '/simulado/create'],
        ];
    }
}
