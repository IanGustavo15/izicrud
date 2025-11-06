<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $items = Cliente::where('deleted', 0)->orderBy('id', 'desc')->paginate(9);
        $allItems = Cliente::where('deleted', 0)->orderBy('id', 'desc')->get();

        return inertia('Cliente/index', [
            'itens' => $items,
            'allItens' => $allItems,
            'totalItensDeletados' => Cliente::where('deleted', 1)->count(),
            'sidebarNavItems' => $this->getSidebarNavItems(),
        ]);
    }


        public function create()
    {





        return inertia('Cliente/create', [
            'sidebarNavItems' => $this->getSidebarNavItems()




        ]);
    }


    public function store(Request $request)
    {

        $request->validate([
            'nome' => 'required|string',
            'cpf' => ['required', 'string', 'max:14', 'regex:/^\d{3}.\d{3}.\d{3}-\d{2}$/'],
            'telefone' => ['required', 'string'],
            'email' => 'required|email',
        ]);


        Cliente::create($request->all());

        return redirect()->route('cliente.index')->with('success', 'Cliente criado com sucesso.');
    }

        public function edit(Cliente $cliente)
    {
        if ($cliente->deleted) {
            return redirect()->route('cliente.index')->with('error', 'Cliente excluído.');
        }






        return inertia('Cliente/create', [
            'item' => $cliente->toArray(),
            'sidebarNavItems' => $this->getSidebarNavItems()




        ]);
    }


    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nome' => 'required|string',
            'cpf' => 'required|string|max:14',
            'telefone' => 'required|string|max:20',
            'email' => 'required|email',
        ]);

        $cliente->update($request->all());

        return redirect()->route('cliente.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->update(['deleted' => 1]);

        return redirect()->route('cliente.index')->with('success', 'Cliente excluído com sucesso.');
    }

    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Clientes', 'href' => '/cliente'],
            ['title' => 'Criar Novo Cliente', 'href' => '/cliente/create'],
        ];
    }
}
