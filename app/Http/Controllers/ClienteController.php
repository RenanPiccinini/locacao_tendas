<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        $query = Cliente::withCount('pedidos')->latest();

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where('nome', 'like', "%{$busca}%")
                  ->orWhere('telefone', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%")
                  ->orWhere('cidade', 'like', "%{$busca}%")
                  ->orWhere('cep', 'like', "%{$busca}%");
        }

        $clientes = $query->paginate(15);
        return view('clientes.index', compact('clientes'));
    }

    public function create(): View
    {
        return view('clientes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('rua') && !$request->filled('endereco')) {
            $request->merge(['endereco' => $request->rua]);
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'cpf_cnpj' => 'nullable|string|max:30',
            'cep' => 'nullable|string|max:20',
            'endereco' => 'required|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:10',
            'observacoes' => 'nullable|string',
        ], [
            'nome.required' => 'O nome ou razão social é obrigatório.',
            'telefone.required' => 'O telefone/WhatsApp é obrigatório.',
            'endereco.required' => 'A Rua/Avenida do endereço é obrigatória.',
        ]);

        if (!empty($validated['estado'])) {
            $validated['estado'] = strtoupper(trim($validated['estado']));
        }

        Cliente::create($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function edit(Cliente $cliente): View
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        if ($request->filled('rua') && !$request->filled('endereco')) {
            $request->merge(['endereco' => $request->rua]);
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'cpf_cnpj' => 'nullable|string|max:30',
            'cep' => 'nullable|string|max:20',
            'endereco' => 'required|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:10',
            'observacoes' => 'nullable|string',
        ], [
            'nome.required' => 'O nome ou razão social é obrigatório.',
            'telefone.required' => 'O telefone/WhatsApp é obrigatório.',
            'endereco.required' => 'A Rua/Avenida do endereço é obrigatória.',
        ]);

        if (!empty($validated['estado'])) {
            $validated['estado'] = strtoupper(trim($validated['estado']));
        }

        $cliente->update($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente excluído com sucesso!');
    }
}
