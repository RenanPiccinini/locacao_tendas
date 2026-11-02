<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">Cadastrar Nova Tenda no Estoque</h2>
            <a href="{{ route('tendas.index') }}" class="text-sm font-semibold text-emerald-700 dark:text-amber-400 hover:text-emerald-900 dark:hover:text-amber-300">&larr; Voltar ao Estoque</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('tendas.store') }}" method="POST" class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Nome da Tenda *</label>
                    <input type="text" name="nome" placeholder="Ex: Tenda Piramidal Branca" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Tamanho / Dimensões *</label>
                    <input type="text" name="tamanho" placeholder="Ex: 5x5m, 10x10m, 3x3m Sanfonada" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Quantidade Total no Estoque *</label>
                        <input type="number" name="quantidade_total" value="1" min="1" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Valor da Diária (R$)</label>
                        <input type="number" step="0.01" name="valor_diaria" value="0.00" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Descrição / Detalhes</label>
                    <textarea name="descricao" rows="2" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm" placeholder="Ex: Lona anti-chamas, estrutura em aço galvanizado"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100 dark:border-[#083d2c]">
                    <a href="{{ route('tendas.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-[#04291c] text-gray-700 dark:text-emerald-200 rounded-lg text-sm font-semibold hover:bg-gray-200 dark:hover:bg-[#073827]">Cancelar</a>
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 rounded-lg text-sm font-bold border border-amber-400/40">Salvar Tenda</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
