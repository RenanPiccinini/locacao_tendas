<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::latest();

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('name', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%")
                  ->orWhere('whatsapp', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('ativo', $request->status === 'ativo');
        }

        $usuarios = $query->paginate(15)->withQueryString();

        $totalUsuarios = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalMontadores = User::where('role', 'montador')->count();
        $totalBloqueados = User::where('ativo', false)->count();

        return view('usuarios.index', compact(
            'usuarios',
            'totalUsuarios',
            'totalAdmins',
            'totalMontadores',
            'totalBloqueados'
        ));
    }

    public function create(): View
    {
        return view('usuarios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => ['required', Rule::in(['admin', 'montador'])],
            'whatsapp' => 'nullable|string|max:30',
            'password' => 'required|string|min:6|confirmed',
            'ativo' => 'nullable|boolean',
        ], [
            'name.required' => 'Informe o nome completo do usuário.',
            'name.max' => 'O nome não pode ultrapassar 255 caracteres.',
            'email.required' => 'Informe o endereço de e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado no sistema.',
            'role.required' => 'Selecione a função/perfil do usuário.',
            'role.in' => 'O perfil selecionado é inválido.',
            'password.required' => 'Informe uma senha.',
            'password.min' => 'A senha deve conter no mínimo :min caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'whatsapp' => !empty($validated['whatsapp']) ? preg_replace('/\D/', '', $validated['whatsapp']) : null,
            'password' => Hash::make($validated['password']),
            'ativo' => $request->has('ativo') ? (bool)$request->ativo : true,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function edit(User $usuario): View
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'role' => ['required', Rule::in(['admin', 'montador'])],
            'whatsapp' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6|confirmed',
            'ativo' => 'nullable|boolean',
        ], [
            'name.required' => 'Informe o nome completo do usuário.',
            'name.max' => 'O nome não pode ultrapassar 255 caracteres.',
            'email.required' => 'Informe o endereço de e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso por outro usuário.',
            'role.required' => 'Selecione a função/perfil do usuário.',
            'role.in' => 'O perfil selecionado é inválido.',
            'password.min' => 'A nova senha deve conter no mínimo :min caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ]);

        // Proteção: Administrador não pode remover seu próprio perfil de admin
        if ($usuario->id === Auth::id() && $validated['role'] !== 'admin') {
            return back()->withInput()->with('error', 'Você não pode rebaixar o seu próprio perfil de Administrador.');
        }

        // Proteção: Administrador não pode bloquear seu próprio acesso
        if ($usuario->id === Auth::id() && $request->has('ativo') && !(bool)$request->ativo) {
            return back()->withInput()->with('error', 'Você não pode bloquear o seu próprio acesso.');
        }

        $dados = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'whatsapp' => !empty($validated['whatsapp']) ? preg_replace('/\D/', '', $validated['whatsapp']) : null,
            'ativo' => $request->has('ativo') ? (bool)$request->ativo : true,
        ];

        if ($request->filled('password')) {
            $dados['password'] = Hash::make($validated['password']);
        }

        $usuario->update($dados);

        return redirect()->route('usuarios.index')
            ->with('success', "Dados do usuário {$usuario->name} atualizados com sucesso!");
    }

    /**
     * Alterna o status de bloqueio do usuário (Bloquear / Liberar acesso)
     */
    public function toggleBloqueio(User $usuario): RedirectResponse
    {
        // Proteção: não pode bloquear o próprio usuário logado
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'Ação não permitida! Você não pode bloquear o seu próprio usuário.');
        }

        $novoStatus = !$usuario->isAtivo();
        $usuario->update(['ativo' => $novoStatus]);

        $statusMsg = $novoStatus ? 'liberado' : 'bloqueado';
        return back()->with('success', "Acesso do usuário {$usuario->name} {$statusMsg} com sucesso!");
    }

    /**
     * Altera diretamente a senha de qualquer usuário (incluindo outros admins)
     */
    public function alterarSenha(Request $request, User $usuario): RedirectResponse
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Informe a nova senha.',
            'password.min' => 'A senha deve conter no mínimo :min caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ]);

        $usuario->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', "Senha do usuário {$usuario->name} alterada com sucesso!");
    }

    /**
     * Exclui um usuário do sistema
     */
    public function destroy(User $usuario): RedirectResponse
    {
        // Proteção: não pode excluir o próprio usuário logado
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'Ação não permitida! Você não pode excluir a sua própria conta.');
        }

        $nome = $usuario->name;
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', "Usuário {$nome} excluído com sucesso!");
    }
}

