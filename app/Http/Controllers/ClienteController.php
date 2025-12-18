<?php

namespace App\Http\Controllers;

use App\Models\Cliente;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $data = $request->validate([
            'nome' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'cpf' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:255',
            'foto' => 'nullable|file|max:10240',
        ]);

        // Handle file uploads if necessary
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('uploads', 'public');
        }

        // Remover campos pivot dos dados principais
        // Dados validados, prosseguir com criação
        $model = Cliente::create($data);
        

        return redirect()->route('cliente.index')->with('success', 'Cliente criado com sucesso!');
    }

        public function edit(Cliente $cliente)
    {
        if ($cliente->deleted) {
            return redirect()->route('cliente.index')->with('error', 'Cliente excluído.');
        }

        

        // Preparar dados para edição
        $itemData = $cliente->toArray();
        // Dados carregados para edição

        return inertia('Cliente/create', [
            'item' => $itemData,
            'sidebarNavItems' => $this->getSidebarNavItems()
            
        ]);
    }


        public function update(Request $request, Cliente $cliente)
    {
        $validationRules = [
            'nome' => 'nullable|string|max:255',
            'email' => 'nullable',
            'cpf' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:255',
        ];

        // Adicionar validação para arquivos apenas se estão sendo enviados
        if ($request->hasFile('foto')) {
            $validationRules['foto'] = 'nullable|file|max:10240';
        }

        $data = $request->validate($validationRules);

        // Handle file uploads if necessary
        if ($request->hasFile('foto')) {
            // Se há um arquivo antigo, remover
            if ($oldFile = $cliente->foto) {
                Storage::disk('public')->delete($oldFile);
            }
            $data['foto'] = $request->file('foto')->store('uploads', 'public');
        }

        // Se há arquivos para remover, processar primeiro
        if ($request->has('filesToRemove') && is_array($request->filesToRemove)) {
            foreach ($request->filesToRemove as $removal) {
                if ($removal['field'] === 'foto' && !isset($removal['index'])) {
                    // Arquivo único
                    if ($cliente->foto && Storage::disk('public')->exists($cliente->foto)) {
                        Storage::disk('public')->delete($cliente->foto);
                    }
                    $data['foto'] = null;
                }
            }
        }


        // Remover campos pivot dos dados principais
        // Dados validados, prosseguir com criação
        $cliente->update($data);
        

        return redirect()->route('cliente.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->update(['deleted' => 1]);

        return redirect()->route('cliente.index')->with('success', 'Cliente excluído com sucesso.');
    }    private function getSidebarNavItems(): array
    {
        return [
            ['title' => 'Todos os Clientes', 'href' => '/cliente'],
            ['title' => 'Criar Novo Cliente', 'href' => '/cliente/create'],
        ];
    }


}
