<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use App\Models\Peca;
use Illuminate\Http\Request;
use App\Models\PecaServico;

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


        $pecas = Peca::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->descricao,
                    'preco_de_venda' => $item->preco_de_venda,
                    'quantidade' => $item->quantidade,
                ];
            });
        $quantiaPeca = PecaServico::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'quantidade_peca' => $item->quantidade_peca,
                ];
            });

            // dd($pecas);
            // dd($quantiaPeca);



        return inertia('Servico/create', [
            'sidebarNavItems' => $this->getSidebarNavItems(),
            'pecas' => $pecas,
            'quantiaPeca' => $quantiaPeca


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

        $serv = Servico::create($request->all());


        if ($request->has('pecas')) {
            foreach ($request->pecas as $peca) {
            PecaServico::create([

                'id_servico' => $serv->id,
                'id_peca' => $peca['value'],
                'quantidade_peca' => 0,
                ]
            );
        }
        }


        // dd($serv);
        // dd($request->pecas);

        return redirect()->route('servico.index')->with('success', 'Serviço criado com sucesso.');
    }

        public function edit(Servico $servico)
    {
        if ($servico->deleted) {
            return redirect()->route('servico.index')->with('error', 'Servico excluído.');
        }



        $pecas = Peca::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->descricao,
                    'preco_de_venda' => $item->preco_de_venda,
                    'quantidade' => $item->quantidade,
                ];
            });

        $pecasEdit = PecaServico::where('deleted', 0)->where('id_servico', $servico->id)->with('peca')->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->peca->id,
                    'label' => $item->peca->descricao,
                    'preco_de_venda' => $item->peca->preco_de_venda,
                    'quantidade' => $item->peca->quantidade,
                ];
            });



        return inertia('Servico/create', [
            'item' => $servico->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
            'pecas' => $pecas,
            'pecasEdit' => $pecasEdit,




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

        $servico->update($request->except('pecas'));
        $pecasDoFormulario = $request->input('pecas', []);
        $idsDasPecasDoFormulario = collect($pecasDoFormulario)->pluck('value')->filter();
        PecaServico::where('id_servico', $servico->id)->whereNotIn('id_peca', $idsDasPecasDoFormulario)->delete();

        if ($request->has('pecas')) {
            foreach ($request->pecas as $peca) {
                if (isset($peca['value']) && $peca['value'] > 0) {
                    PecaServico::updateOrCreate([
                        'id_servico' => $servico->id,
                        'id_peca' => $peca['value'],
                    ],
                    [
                        'quantidade_peca' => 1,
                    ]
                );
                }
            }
        }
        // dd($request->pecas);

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
