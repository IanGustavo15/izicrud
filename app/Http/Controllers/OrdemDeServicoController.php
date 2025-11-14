<?php

namespace App\Http\Controllers;

use App\Models\OrdemDeServico;
use App\Models\Servico;
use App\Models\Veiculo;
use App\Models\Cliente;
use App\Models\Peca;
use Illuminate\Http\Request;
use App\Models\ServicoOrdemDeServico;

class OrdemDeServicoController extends Controller
{
    public function index()
    {
        $items = OrdemDeServico::where('deleted', 0)->with('cliente')->with('veiculo')->orderBy('id', 'desc')->paginate(9);
        $allItems = OrdemDeServico::where('deleted', 0)->with('cliente')->with('veiculo')->orderBy('id', 'desc')->get();
        // dd($items);

        return inertia('OrdemDeServico/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => OrdemDeServico::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {
        $id_clienteOptions = Cliente::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        $id_veiculoOptions = Veiculo::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->modelo // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });

            $servicos = Servico::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome,
                    'descricao' => $item->descricao,
                    'tempo' => $item->tempo_estimado,
                ];
            });

            // dd($servicos);


        return inertia('OrdemDeServico/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_clienteOptions' => $id_clienteOptions
            ,'id_veiculoOptions' => $id_veiculoOptions
            , 'servicos' => $servicos





        ]);
    }


    public function store(Request $request)
    {

        $request->validate([
            'id_cliente' => 'required|integer',
            'id_veiculo' => 'required|integer',
            'data_de_entrada' => 'required|date',
            'data_de_saida' => 'required|date',
            'status' => 'required|integer|min:1|max:4', // Isso impede que o status não seja obrigatório
            'valor_total' => 'required|numeric',
            'observacao' => 'required|string',
        ]);

        $os = OrdemDeServico::create($request->all());

        foreach ($request->servicos as $servico) {
            ServicoOrdemDeServico::create([

                'id_ordemdeservico' => $os->id,
                'id_servico' => $servico['value'],
                'quantidade' => 0,
                'preco_unitario' => 0,

                ]
            );
        }
        // dd($os);
        // dd($request->servicos);

        return redirect()->route('ordemdeservico.index')->with('success', 'Ordem de Serviço criada com sucesso.');
    }

        public function edit(OrdemDeServico $ordemdeservico)
    {
        if ($ordemdeservico->deleted) {
            return redirect()->route('ordemdeservico.index')->with('error', 'Ordem de Serviço excluída.');
        }
        // dd($ordemdeservico);

        $id_clienteOptions = \App\Models\Cliente::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });
        $id_veiculoOptions = \App\Models\Veiculo::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->modelo // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });

            $servicos = Servico::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->nome,
                    'descricao' => $item->descricao, // TODO: Ajustar o campo 'nome' conforme o modelo relacionado
                ];
            });

            $id_servicoEdit = \App\Models\ServicoOrdemDeServico::where('deleted', 0)->where('id_ordemdeservico', $ordemdeservico->id)->with('servico')->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->servico->id,
                    'label' => $item->servico->nome,
                    'descricao' => $item->servico->descricao,
                    'quantidade' => $item->quantidade,
                    'preco_unitario' => $item->preco_unitario,
                ];
            });

            // dd($id_servicoEdit);
            // dd($servicos);

        return inertia('OrdemDeServico/create', [
            'item' => $ordemdeservico->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()
            ,'id_clienteOptions' => $id_clienteOptions
            ,'id_veiculoOptions' => $id_veiculoOptions
            , 'servicos' => $servicos
            , 'id_servicoEdit' => $id_servicoEdit





        ]);
    }


    public function update(Request $request, OrdemDeServico $ordemdeservico)
    {
        $request->validate([
            'id_cliente' => 'required|integer',
            'id_veiculo' => 'required|integer',
            'data_de_entrada' => 'required|date',
            'data_de_saida' => 'required|date',
            'status' => 'required|integer|min:1|max:4', // Isso impede que o status não seja obrigatório
            'valor_total' => 'required|numeric',
            'observacao' => 'required|string',
            'servicos' => 'present|array',
        ]);

        $ordemdeservico->update($request->except('servicos'));
        $servicosDoFormulario = $request->input('servicos', []);
        $idsDosServicosDoFormulario = collect($servicosDoFormulario)->pluck('value')->filter();
        ServicoOrdemDeServico::where('id_ordemdeservico', $ordemdeservico->id)->whereNotIn('id_servico', $idsDosServicosDoFormulario)->delete();

        // dd($ordemdeservico);
        // dd($os);

        // dd($request->servicos);
        if ($request->has('servicos')) {
            foreach ($request->servicos as $servico) {
                if (isset($servico['value']) && $servico['value'] > 0) {
                    ServicoOrdemDeServico::updateOrCreate([
                        'id_ordemdeservico' => $ordemdeservico->id,
                        'id_servico' => $servico['value'],
                    ],
                    [
                        'quantidade' => 0,
                        'preco_unitario' => 0,
                    ]
                );
                }
            }
        }

        return redirect()->route('ordemdeservico.index')->with('success', 'Ordem de Serviço atualizada com sucesso.');
    }

    public function destroy(OrdemDeServico $ordemdeservico)
    {
        $ordemdeservico->update(['deleted' => 1]);

        return redirect()->route('ordemdeservico.index')->with('success', 'Ordem de Serviço excluída com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos as Ordens de Serviço', 'href' => '/ordemdeservico'],
            ['title' => 'Criar Nova Ordem de Serviço', 'href' => '/ordemdeservico/create'],
            ['title' => '-----------------------------', 'href' => '/ordemdeservico'],
            ['title' => 'Adicionar Novo Cliente', 'href' => '/cliente/create'],
            ['title' => 'Adicionar Novo Veículo', 'href' => '/veiculo/create'],
        ];
    }

    public function getVeiculoPorCliente($id_cliente)
    {
        $veiculo = Veiculo::where('deleted', 0)->where('id_cliente', $id_cliente)->orderBy('id', 'desc')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->modelo
                ];
            });
        return $veiculo;
    }
}
