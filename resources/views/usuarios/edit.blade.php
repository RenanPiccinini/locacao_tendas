<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                    Editar Usuário: {{ $usuario->name }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-emerald-400/80">Atualize dados cadastrais, perfil de acesso ou status de bloqueio</p>
            </div>
            <a href="{{ route('usuarios.index') }}" class="text-sm font-semibold text-emerald-700 dark:text-amber-400 hover:text-emerald-900 dark:hover:text-amber-300">&larr; Voltar aos Usuários</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="bg-white dark:bg-[#031d14] p-6 sm:p-8 rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c] space-y-5">
                @csrf
                @method('PUT')

                <!-- Nome Completo -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Nome Completo *</label>
                    <input type="text" name="name" value="{{ old('name', $usuario->name) }}" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-red-500 font-semibold" />
                </div>

                <!-- E-mail de Acesso -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">E-mail de Acesso *</label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500 font-semibold" />
                </div>

                <!-- Perfil e WhatsApp -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Perfil / Função no Sistema *</label>
                        <select name="role" required {{ $usuario->id === Auth::id() ? 'disabled' : '' }} class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-medium">
                            <option value="montador" {{ old('role', $usuario->role) === 'montador' ? 'selected' : '' }}>Montador (Equipe de Campo)</option>
                            <option value="admin" {{ old('role', $usuario->role) === 'admin' ? 'selected' : '' }}>Administrador (Acesso Total)</option>
                        </select>
                        @if($usuario->id === Auth::id())
                            <input type="hidden" name="role" value="admin">
                            <p class="text-[11px] text-amber-500 mt-1">Você não pode alterar seu próprio perfil de administrador.</p>
                        @endif
                        <x-input-error :messages="$errors->get('role')" class="mt-1 text-xs text-red-500 font-semibold" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">WhatsApp (com DDD)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600 dark:text-amber-400 font-bold text-xs">
                                +55
                            </span>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp', $usuario->whatsapp) }}" placeholder="17991429132" class="w-full pl-12 bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                        <x-input-error :messages="$errors->get('whatsapp')" class="mt-1 text-xs text-red-500 font-semibold" />
                    </div>
                </div>

                <!-- Alterar Senha (Opcional) -->
                <div class="border-t border-gray-200 dark:border-[#083d2c] pt-5 space-y-4">
                    <p class="text-xs text-gray-500 dark:text-amber-400 font-semibold uppercase">Alterar Senha (deixe em branco para não alterar)</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Nova Senha</label>
                            <input type="password" name="password" minlength="6" placeholder="Mínimo 6 caracteres" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                            <p class="text-[11px] text-gray-400 dark:text-emerald-400/60 mt-1">Mínimo de 6 caracteres (opcional).</p>
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500 font-semibold" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Confirmar Nova Senha</label>
                            <input type="password" name="password_confirmation" minlength="6" placeholder="Repita a nova senha" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                    </div>
                </div>

                <!-- Status de Acesso (Bloquear / Liberar) -->
                <div class="p-4 {{ $usuario->id === Auth::id() ? 'bg-gray-50 dark:bg-[#021811] opacity-80' : 'bg-emerald-50 dark:bg-[#04291c]' }} rounded-xl border border-emerald-100 dark:border-[#083d2c] flex items-center justify-between">
                    <div>
                        <label for="ativo" class="text-sm font-bold text-gray-900 dark:text-white cursor-pointer block">Acesso Liberado</label>
                        <p class="text-xs text-gray-500 dark:text-emerald-300/70">
                            @if($usuario->id === Auth::id())
                                Você não pode bloquear o seu próprio usuário conectado.
                            @else
                                Desmarque para bloquear imediatamente o acesso deste usuário ao sistema.
                            @endif
                        </p>
                    </div>
                    @if($usuario->id === Auth::id())
                        <input type="hidden" name="ativo" value="1">
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Ativo</span>
                    @else
                        <input type="hidden" name="ativo" value="0">
                        <input type="checkbox" id="ativo" name="ativo" value="1" {{ old('ativo', $usuario->isAtivo() ? '1' : '0') == '1' ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-amber-400 w-5 h-5 cursor-pointer">
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-[#083d2c]">
                    <a href="{{ route('usuarios.index') }}" class="px-5 py-2.5 bg-gray-100 dark:bg-[#04291c] text-gray-700 dark:text-emerald-200 font-semibold rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-[#073827] transition">Cancelar</a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 font-bold rounded-lg text-sm shadow-sm transition border border-amber-400/40">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

