<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                    Gestão de Usuários
                </h2>
                <p class="text-sm text-gray-500 dark:text-emerald-300/70">
                    Controle de acessos, bloqueio/liberação, permissões e alteração de senhas
                </p>
            </div>
            <a href="{{ route('usuarios.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 font-bold rounded-lg shadow-sm text-sm transition border border-amber-400/40 w-fit">
                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Novo Usuário
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Cards Resumo / Estatísticas -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-[#031d14] p-5 rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-emerald-400/70 font-semibold uppercase">Total Usuários</p>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $totalUsuarios }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-amber-400 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#031d14] p-5 rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-amber-600 dark:text-amber-400 font-semibold uppercase">Administradores</p>
                            <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $totalAdmins }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#031d14] p-5 rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold uppercase">Montadores</p>
                            <h3 class="text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-1">{{ $totalMontadores }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#031d14] p-5 rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-red-500 font-semibold uppercase">Acesso Bloqueado</p>
                            <h3 class="text-2xl font-black text-red-500 mt-1">{{ $totalBloqueados }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-red-500/10 text-red-500 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros e Busca -->
            <div class="bg-white dark:bg-[#031d14] p-4 rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c]">
                <form method="GET" action="{{ route('usuarios.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                    <div class="flex-1 max-w-md relative">
                        <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por nome, e-mail ou WhatsApp..." class="w-full pl-10 pr-4 py-2 bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <select name="role" onchange="this.form.submit()" class="bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 text-xs rounded-lg focus:ring-amber-400 focus:border-amber-400 py-2">
                            <option value="">Todos os Perfis</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administradores</option>
                            <option value="montador" {{ request('role') === 'montador' ? 'selected' : '' }}>Montadores</option>
                        </select>

                        <select name="status" onchange="this.form.submit()" class="bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 text-xs rounded-lg focus:ring-amber-400 focus:border-amber-400 py-2">
                            <option value="">Todos os Status</option>
                            <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Liberados (Ativos)</option>
                            <option value="bloqueado" {{ request('status') === 'bloqueado' ? 'selected' : '' }}>Bloqueados</option>
                        </select>

                        <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-bold transition">
                            Filtrar
                        </button>
                        @if(request()->hasAny(['busca', 'role', 'status']))
                            <a href="{{ route('usuarios.index') }}" class="text-xs text-gray-500 dark:text-amber-400 hover:underline px-2 py-1">Limpar</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabela de Usuários -->
            <div class="bg-white dark:bg-[#031d14] rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-[#083d2c]">
                        <thead class="bg-gray-50 dark:bg-[#021811]">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-emerald-400 uppercase tracking-wider">Usuário</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-emerald-400 uppercase tracking-wider">Perfil</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-emerald-400 uppercase tracking-wider">WhatsApp</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-emerald-400 uppercase tracking-wider">Status do Acesso</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-emerald-400 uppercase tracking-wider">Cadastro</th>
                                <th class="px-6 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-emerald-400 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-[#083d2c] text-sm">
                            @forelse($usuarios as $user)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-[#04291c]/50 transition {{ $user->isBloqueado() ? 'bg-red-50/30 dark:bg-red-950/20' : '' }}">
                                    <!-- Nome e E-mail -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm {{ $user->isAdmin() ? 'bg-amber-500/20 text-amber-500 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-500 border border-emerald-500/30' }}">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                                    {{ $user->name }}
                                                    @if($user->id === Auth::id())
                                                        <span class="text-[10px] px-1.5 py-0.2 rounded font-bold bg-amber-400 text-slate-950">Você</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-emerald-300/70">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Perfil -->
                                    <td class="px-6 py-4">
                                        @if($user->isAdmin())
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                                <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                Administrador
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                                <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                                Montador
                                            </span>
                                        @endif
                                    </td>

                                    <!-- WhatsApp -->
                                    <td class="px-6 py-4">
                                        @if($user->whatsapp)
                                            <a href="https://wa.me/55{{ preg_replace('/\D/', '', $user->whatsapp) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                {{ $user->whatsapp }}
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">Não informado</span>
                                        @endif
                                    </td>

                                    <!-- Status do Acesso -->
                                    <td class="px-6 py-4">
                                        @if($user->isAtivo())
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-emerald-950 dark:text-emerald-300 border border-green-300 dark:border-emerald-700/50">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Acesso Liberado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300 border border-red-300 dark:border-red-700/50">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                Bloqueado
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Cadastro -->
                                    <td class="px-6 py-4 text-xs text-gray-500 dark:text-emerald-400/70">
                                        {{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}
                                    </td>

                                    <!-- Ações -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Botão Bloquear / Liberar Acesso -->
                                            @if($user->id === Auth::id())
                                                <span class="p-1.5 text-gray-300 dark:text-gray-600 cursor-not-allowed" title="Você não pode bloquear o seu próprio usuário">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                </span>
                                            @else
                                                <form action="{{ route('usuarios.toggle-bloqueio', $user) }}" method="POST" class="inline" data-confirm="Deseja realmente <strong>{{ $user->isAtivo() ? 'BLOQUEAR' : 'LIBERAR' }}</strong> o acesso de <strong>{{ $user->name }}</strong>?" data-confirm-title="{{ $user->isAtivo() ? 'Bloquear Acesso' : 'Liberar Acesso' }}" data-confirm-btn="Sim, {{ $user->isAtivo() ? 'Bloquear' : 'Liberar' }}" data-confirm-type="{{ $user->isAtivo() ? 'danger' : 'success' }}">
                                                    @csrf
                                                    <button type="submit" class="p-1.5 rounded transition {{ $user->isAtivo() ? 'text-amber-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40' : 'text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-950/40' }}" title="{{ $user->isAtivo() ? 'Bloquear Acesso deste Usuário' : 'Liberar Acesso deste Usuário' }}">
                                                        @if($user->isAtivo())
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                        @else
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        @endif
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Botão Alterar Senha (Modal Rápido) -->
                                            <button type="button" onclick="abrirModalSenha({{ $user->id }}, '{{ addslashes($user->name) }}')" class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-950/40 rounded transition" title="Alterar Senha do Usuário">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                            </button>

                                            <!-- Botão Editar -->
                                            <a href="{{ route('usuarios.edit', $user) }}" class="p-1.5 text-gray-500 dark:text-emerald-300 hover:text-emerald-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#063325] rounded transition" title="Editar Usuário">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>

                                            <!-- Botão Excluir -->
                                            @if($user->id === Auth::id())
                                                <span class="p-1.5 text-gray-300 dark:text-gray-600 cursor-not-allowed" title="Você não pode excluir a sua própria conta">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </span>
                                            @else
                                                <form action="{{ route('usuarios.destroy', $user) }}" method="POST" class="inline" data-confirm="Tem certeza que deseja excluir o usuário <strong>{{ $user->name }}</strong> ({{ $user->email }})?" data-confirm-title="Excluir Usuário" data-confirm-btn="Sim, Excluir Usuário" data-confirm-type="danger">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/40 rounded transition" title="Excluir Usuário">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-emerald-300/60">
                                        Nenhum usuário encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($usuarios->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-[#083d2c]">
                        {{ $usuarios->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Modal Rápido de Alteração de Senha -->
    <div id="modal-alterar-senha" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#031d14] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 dark:border-[#083d2c] space-y-5 animate-scale-up">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#083d2c] pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">Alterar Senha de Acesso</h3>
                </div>
                <button type="button" onclick="fecharModalSenha()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-lg font-bold">&times;</button>
            </div>

            <form id="form-alterar-senha" method="POST" class="space-y-4">
                @csrf
                <p class="text-xs text-gray-600 dark:text-emerald-300/80">
                    Definindo nova senha para o usuário: <strong id="modal-usuario-nome" class="text-gray-900 dark:text-white"></strong>
                </p>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Nova Senha *</label>
                    <input type="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Confirmar Nova Senha *</label>
                    <input type="password" name="password_confirmation" required minlength="6" placeholder="Repita a nova senha" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-[#083d2c]">
                    <button type="button" onclick="fecharModalSenha()" class="px-4 py-2 bg-gray-100 dark:bg-[#04291c] text-gray-700 dark:text-emerald-200 font-semibold rounded-lg text-xs hover:bg-gray-200 dark:hover:bg-[#073827] transition">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs shadow-xs transition">
                        Atualizar Senha
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalSenha(usuarioId, usuarioNome) {
            const modal = document.getElementById('modal-alterar-senha');
            const form = document.getElementById('form-alterar-senha');
            const nomeSpan = document.getElementById('modal-usuario-nome');

            nomeSpan.textContent = usuarioNome;
            form.action = `/usuarios/${usuarioId}/alterar-senha`;

            modal.classList.remove('hidden');
        }

        function fecharModalSenha() {
            const modal = document.getElementById('modal-alterar-senha');
            modal.classList.add('hidden');
        }
    </script>
</x-app-layout>

