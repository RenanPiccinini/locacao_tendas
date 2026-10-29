<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class MontadorController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'montador')
            ->withCount(['pedidosComoMontador as total_pedidos', 'pedidosComoMontador as pedidos_ativos' => function ($q) {
                $q->whereNotIn('status', ['concluido', 'cancelado']);
            }])
            ->latest();

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('name', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%")
                  ->orWhere('whatsapp', 'like', "%{$busca}%");
            });
        }

        $montadores = $query->paginate(15);

        return view('montadores.index', compact('montadores'));
    }

    public function create(): View
    {
        return view('montadores.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'whatsapp' => 'required|string|max:30',
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'name.required' => 'Informe o nome completo do montador.',
            'email.required' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado no sistema.',
            'whatsapp.required' => 'Informe o número de WhatsApp (com DDD).',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'whatsapp' => preg_replace('/\D/', '', $validated['whatsapp']),
            'password' => Hash::make($validated['password']),
            'role' => 'montador',
        ]);

        return redirect()->route('montadores.index')
            ->with('success', 'Montador cadastrado com sucesso!');
    }

    public function edit(User $montadore): View
    {
        if ($montadore->role !== 'montador') {
            abort(404);
        }

        return view('montadores.edit', ['montador' => $montadore]);
    }

    public function update(Request $request, User $montadore): RedirectResponse
    {
        if ($montadore->role !== 'montador') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($montadore->id)],
            'whatsapp' => 'required|string|max:30',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $dados = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'whatsapp' => preg_replace('/\D/', '', $validated['whatsapp']),
        ];

        if ($request->filled('password')) {
            $dados['password'] = Hash::make($validated['password']);
        }

        $montadore->update($dados);

        return redirect()->route('montadores.index')
            ->with('success', 'Dados do montador atualizados com sucesso!');
    }

    public function destroy(User $montadore): RedirectResponse
    {
        if ($montadore->role !== 'montador') {
            abort(403);
        }

        // Não permite excluir se tiver montagens em andamento
        $ativos = $montadore->pedidosComoMontador()->whereNotIn('status', ['concluido', 'cancelado'])->count();
        if ($ativos > 0) {
            return back()->with('error', "Não é possível excluir este montador pois ele possui {$ativos} pedido(s) em andamento!");
        }

        $montadore->delete();

        return redirect()->route('montadores.index')
            ->with('success', 'Montador removido com sucesso!');
    }
}
