<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                    Editar Proposta Nº {{ $proposta->numero }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-emerald-300/70">
                    {{ $proposta->para_razao_social }} {{ $proposta->identificacao ? "• {$proposta->identificacao}" : '' }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('propostas.pdf', $proposta) }}" target="_blank" class="px-4 py-2 bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs rounded-lg shadow-xs transition">
                    Ver PDF
                </a>
                <a href="{{ route('propostas.show', $proposta) }}" class="text-sm text-emerald-700 dark:text-amber-400 hover:underline font-semibold">
                    &larr; Voltar aos Detalhes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('propostas.update', $proposta) }}" class="bg-white dark:bg-[#031d14] p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-[#083d2c] space-y-6 transition-colors">
                @csrf
                @method('PUT')

                @include('propostas._form', ['proposta' => $proposta])

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-[#083d2c]">
                    <a href="{{ route('propostas.show', $proposta) }}" class="px-5 py-2.5 bg-gray-100 dark:bg-[#04291c] text-gray-700 dark:text-emerald-200 font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-[#073827] transition text-sm">
                        Cancelar
                    </a>
                    <button type="submit" class="px-7 py-2.5 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 font-bold rounded-lg shadow-sm transition text-sm border border-amber-400/40">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
