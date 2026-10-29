<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">Editar Tenda: {{ $tenda->nome }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('tendas.update', $tenda) }}" method="POST" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nome da Tenda *</label>
                    <input type="text" name="nome" value="{{ old('nome', $tenda->nome) }}" required class="w-full border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tamanho / Dimensões *</label>
                    <input type="text" name="tamanho" value="{{ old('tamanho', $tenda->tamanho) }}" required class="w-full border-gray-300 rounded-lg text-sm">
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Quantidade Total *</label>
                        <input type="number" name="quantidade_total" value="{{ old('quantidade_total', $tenda->quantidade_total) }}" min="1" required class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Qtd. Disponível *</label>
                        <input type="number" name="quantidade_disponivel" value="{{ old('quantidade_disponivel', $tenda->quantidade_disponivel) }}" min="0" required class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Diária (R$)</label>
                        <input type="number" step="0.01" name="valor_diaria" value="{{ old('valor_diaria', $tenda->valor_diaria) }}" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Descrição</label>
                    <textarea name="descricao" rows="2" class="w-full border-gray-300 rounded-lg text-sm">{{ old('descricao', $tenda->descricao) }}</textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('tendas.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold">Cancelar</a>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold">Atualizar Tenda</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

