<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">Cadastrar Novo Usuário</h2>
                <p class="text-sm text-gray-500 dark:text-emerald-400/80">Crie o acesso de um novo administrador ou montador no sistema</p>
            </div>
            <a href="{{ route('usuarios.index') }}" class="text-sm font-semibold text-emerald-700 dark:text-amber-400 hover:text-emerald-900 dark:hover:text-amber-300">&larr; Voltar aos Usuários</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('usuarios.store') }}" method="POST" class="bg-white dark:bg-[#031d14] p-6 sm:p-8 rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c] space-y-5">
                @csrf

                <!-- Nome Completo -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Nome Completo *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Roberto Piccinini" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-red-500 font-semibold" />
                </div>

                <!-- E-mail de Acesso -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">E-mail de Acesso *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="roberto@tendaseldorado.com.br" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500 font-semibold" />
                </div>

                <!-- Perfil e WhatsApp -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Perfil / Função no Sistema *</label>
                        <select name="role" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-medium">
                            <option value="montador" {{ old('role') === 'montador' ? 'selected' : '' }}>Montador (Equipe de Campo)</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador (Acesso Total)</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-1 text-xs text-red-500 font-semibold" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">WhatsApp (com DDD)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600 dark:text-amber-400 font-bold text-xs">
                                +55
                            </span>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="17991429132" class="w-full pl-12 bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                        <x-input-error :messages="$errors->get('whatsapp')" class="mt-1 text-xs text-red-500 font-semibold" />
                    </div>
                </div>

                <!-- Senha e Confirmação -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-200 dark:border-[#083d2c] pt-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Senha de Acesso *</label>
                        <input type="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        <p class="text-[11px] text-gray-400 dark:text-emerald-400/60 mt-1">Mínimo de 6 caracteres.</p>
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500 font-semibold" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Confirmar Senha *</label>
                        <input type="password" name="password_confirmation" required minlength="6" placeholder="Repita a senha" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>
                </div>

                <!-- Status de Acesso -->
                <div class="p-4 bg-emerald-50 dark:bg-[#04291c] rounded-xl border border-emerald-100 dark:border-[#083d2c] flex items-center justify-between">
                    <div>
                        <label for="ativo" class="text-sm font-bold text-gray-900 dark:text-white cursor-pointer block">Acesso Liberado</label>
                        <p class="text-xs text-gray-500 dark:text-emerald-300/70">Se desmarcado, o usuário não conseguirá entrar no sistema.</p>
                    </div>
                    <input type="hidden" name="ativo" value="0">
                    <input type="checkbox" id="ativo" name="ativo" value="1" {{ old('ativo', '1') == '1' ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-amber-400 w-5 h-5 cursor-pointer">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-[#083d2c]">
                    <a href="{{ route('usuarios.index') }}" class="px-5 py-2.5 bg-gray-100 dark:bg-[#04291c] text-gray-700 dark:text-emerald-200 font-semibold rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-[#073827] transition">Cancelar</a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 font-bold rounded-lg text-sm shadow-sm transition border border-amber-400/40">
                        Cadastrar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

