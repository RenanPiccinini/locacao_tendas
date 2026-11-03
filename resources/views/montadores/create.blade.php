<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">Cadastrar Novo Montador</h2>
                <p class="text-sm text-gray-500 dark:text-emerald-400/80">Crie o acesso e configure o WhatsApp para receber as ordens de serviço</p>
            </div>
            <a href="{{ route('montadores.index') }}" class="text-sm font-semibold text-emerald-700 dark:text-amber-400 hover:text-emerald-900 dark:hover:text-amber-300">&larr; Voltar aos Montadores</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('montadores.store') }}" method="POST" class="bg-white dark:bg-[#031d14] p-6 sm:p-8 rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c] space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Nome Completo *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Lucas Ferreira" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-red-500" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">E-mail de Acesso ao Sistema *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="lucas@empresa.com" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">WhatsApp (com DDD) *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600 dark:text-amber-400 font-bold text-xs">
                            +55
                        </span>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" required placeholder="17991429132" class="w-full pl-12 bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>
                    <p class="text-[11px] text-gray-400 dark:text-emerald-400/60 mt-1">É para este número que o robô enviará as O.S., fotos e avisos semanais.</p>
                    <x-input-error :messages="$errors->get('whatsapp')" class="mt-1 text-xs text-red-500" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-200 dark:border-[#083d2c] pt-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Senha Provisória *</label>
                        <input type="password" name="password" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Confirmar Senha *</label>
                        <input type="password" name="password_confirmation" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-[#083d2c]">
                    <a href="{{ route('montadores.index') }}" class="px-5 py-2.5 bg-gray-100 dark:bg-[#04291c] text-gray-700 dark:text-emerald-200 font-semibold rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-[#073827] transition">Cancelar</a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 font-bold rounded-lg text-sm shadow-sm transition border border-amber-400/40">
                        Salvar Montador
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
