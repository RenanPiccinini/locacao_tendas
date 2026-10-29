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
                  ->orWhere('email', 'like', "%{$busca}%");
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
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'cpf_cnpj' => 'nullable|string|max:30',
            'endereco' => 'required|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'cep' => 'nullable|string|max:20',
            'observacoes' => 'nullable|string',
        ]);

        Cliente::create($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function edit(Cliente $cliente): View
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'cpf_cnpj' => 'nullable|string|max:30',
            'endereco' => 'required|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'cep' => 'nullable|string|max:20',
            'observacoes' => 'nullable|string',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente excluído com sucesso!');
    }
}

