<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                    Nova Proposta de Locação
                </h2>
                <p class="text-sm text-gray-500 dark:text-emerald-300/70">
                    Preencha os dados da proposta para salvar e gerar o PDF oficial
                </p>
            </div>

            <a href="{{ route('propostas.index') }}" class="text-sm text-emerald-700 dark:text-amber-400 hover:underline font-semibold">
                &larr; Voltar às Propostas
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('propostas.store') }}" class="bg-white dark:bg-[#031d14] p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-[#083d2c] space-y-6 transition-colors">
                @csrf

                @include('propostas._form')

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-[#083d2c]">
                    <a href="{{ route('propostas.index') }}" class="px-5 py-2.5 bg-gray-100 dark:bg-[#04291c] text-gray-700 dark:text-emerald-200 font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-[#073827] transition text-sm">
                        Cancelar
                    </a>
                    <button type="submit" class="px-7 py-2.5 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 font-bold rounded-lg shadow-sm transition text-sm border border-amber-400/40">
                        Salvar Proposta & Gerar PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
