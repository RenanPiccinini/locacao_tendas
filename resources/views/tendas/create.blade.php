<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">Cadastrar Nova Tenda no Estoque</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('tendas.store') }}" method="POST" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nome da Tenda *</label>
                    <input type="text" name="nome" placeholder="Ex: Tenda Piramidal Branca" required class="w-full border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tamanho / Dimensões *</label>
                    <input type="text" name="tamanho" placeholder="Ex: 5x5m, 10x10m, 3x3m Sanfonada" required class="w-full border-gray-300 rounded-lg text-sm">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Quantidade Total no Estoque *</label>
                        <input type="number" name="quantidade_total" value="1" min="1" required class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Valor da Diária (R$)</label>
                        <input type="number" step="0.01" name="valor_diaria" value="0.00" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Descrição / Detalhes</label>
                    <textarea name="descricao" rows="2" class="w-full border-gray-300 rounded-lg text-sm" placeholder="Ex: Lona anti-chamas, estrutura em aço galvanizado"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('tendas.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold">Cancelar</a>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold">Salvar Tenda</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

