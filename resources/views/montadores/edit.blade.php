<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Editar Montador: {{ $montador->name }}</h2>
                <p class="text-sm text-gray-500">Atualize os dados ou altere o número de WhatsApp</p>
            </div>
            <a href="{{ route('montadores.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">&larr; Voltar</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('montadores.update', $montador) }}" method="POST" class="bg-white p-6 sm:p-8 rounded-xl shadow-xs border border-gray-200 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nome Completo *</label>
                    <input type="text" name="name" value="{{ old('name', $montador->name) }}" required class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm">
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-red-500" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">E-mail de Acesso *</label>
                    <input type="email" name="email" value="{{ old('email', $montador->email) }}" required class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm">
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">WhatsApp (com DDD) *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600 font-bold text-xs">
                            +55
                        </span>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $montador->whatsapp) }}" required class="w-full pl-12 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm">
                    </div>
                    <x-input-error :messages="$errors->get('whatsapp')" class="mt-1 text-xs text-red-500" />
                </div>

                <div class="border-t border-gray-200 pt-5 space-y-4">
                    <p class="text-xs text-gray-500 font-semibold uppercase">Alterar Senha (deixe em branco para não alterar)</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nova Senha</label>
                            <input type="password" name="password" class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm">
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Confirmar Nova Senha</label>
                            <input type="password" name="password_confirmation" class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('montadores.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm shadow-sm transition">
                        Atualizar Montador
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

