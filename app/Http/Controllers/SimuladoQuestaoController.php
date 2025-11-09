<?php

namespace App\Http\Controllers;

use App\Models\SimuladoQuestao;
use Illuminate\Http\Request;

class SimuladoQuestaoController extends Controller
{
    public function index()
    {
        $items = SimuladoQuestao::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = SimuladoQuestao::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('SimuladoQuestao/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => SimuladoQuestao::where('deleted', 1)->count(),
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
        $id_questaoOptions = \App\Models\Questao::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });

        return inertia('SimuladoQuestao/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_simuladoOptions' => $id_simuladoOptions
            ,'id_questaoOptions' => $id_questaoOptions
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_simulado' => 'required|integer|max:255',
            'id_questao' => 'required|integer|max:255',
        ]);

        SimuladoQuestao::create($request->all());

        return redirect()->route('simuladoquestao.index')->with('success', 'SimuladoQuestao criado com sucesso.');
    }

        public function edit(SimuladoQuestao $simuladoquestao)
    {
        if ($simuladoquestao->deleted) {
            return redirect()->route('simuladoquestao.index')->with('error', 'SimuladoQuestao excluído.');
        }

        $id_simuladoOptions = \App\Models\Simulado::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
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

        return inertia('SimuladoQuestao/create', [
            'item' => $simuladoquestao->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_simuladoOptions' => $id_simuladoOptions
            ,'id_questaoOptions' => $id_questaoOptions
        ]);
    }


    public function update(Request $request, SimuladoQuestao $simuladoquestao)
    {
        $request->validate([
            'id_simulado' => 'required|integer|max:255',
            'id_questao' => 'required|integer|max:255',
        ]);

        $simuladoquestao->update($request->all());

        return redirect()->route('simuladoquestao.index')->with('success', 'SimuladoQuestao atualizado com sucesso.');
    }

    public function destroy(SimuladoQuestao $simuladoquestao)
    {
        $simuladoquestao->update(['deleted' => 1]);

        return redirect()->route('simuladoquestao.index')->with('success', 'SimuladoQuestao excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os SimuladoQuestaos', 'href' => '/simuladoquestao'],
            ['title' => 'Criar Novo SimuladoQuestao', 'href' => '/simuladoquestao/create'],
        ];
    }
}
