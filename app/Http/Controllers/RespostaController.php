<?php

namespace App\Http\Controllers;

use App\Models\Resposta;
use Illuminate\Http\Request;

class RespostaController extends Controller
{
    public function index()
    {
        $items = Resposta::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = Resposta::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('Resposta/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Resposta::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {
        $id_inscricaoOptions = \App\Models\Inscricao::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        $id_questaoOptions = \App\Models\Questao::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        
        
        

        return inertia('Resposta/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_inscricaoOptions' => $id_inscricaoOptions
            ,'id_questaoOptions' => $id_questaoOptions
            
            
            
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_inscricao' => 'required|integer|max:255',
            'id_questao' => 'required|integer|max:255',
            'resposta_selecionada' => 'required|string|max:255',
            'tempo_resposta_segundos' => 'required|integer|max:255',
            'correta' => 'required|boolean|max:255',
        ]);

        Resposta::create($request->all());

        return redirect()->route('resposta.index')->with('success', 'Resposta criado com sucesso.');
    }

        public function edit(Resposta $resposta)
    {
        if ($resposta->deleted) {
            return redirect()->route('resposta.index')->with('error', 'Resposta excluído.');
        }

        $id_inscricaoOptions = \App\Models\Inscricao::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        $id_questaoOptions = \App\Models\Questao::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        
        
        

        return inertia('Resposta/create', [
            'item' => $resposta->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_inscricaoOptions' => $id_inscricaoOptions
            ,'id_questaoOptions' => $id_questaoOptions
            
            
            
        ]);
    }


    public function update(Request $request, Resposta $resposta)
    {
        $request->validate([
            'id_inscricao' => 'required|integer|max:255',
            'id_questao' => 'required|integer|max:255',
            'resposta_selecionada' => 'required|string|max:255',
            'tempo_resposta_segundos' => 'required|integer|max:255',
            'correta' => 'required|boolean|max:255',
        ]);

        $resposta->update($request->all());

        return redirect()->route('resposta.index')->with('success', 'Resposta atualizado com sucesso.');
    }

    public function destroy(Resposta $resposta)
    {
        $resposta->update(['deleted' => 1]);

        return redirect()->route('resposta.index')->with('success', 'Resposta excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Respostas', 'href' => '/resposta'],
            ['title' => 'Criar Novo Resposta', 'href' => '/resposta/create'],
        ];
    }
}
