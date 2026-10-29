<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Estoque de Tendas
                </h2>
                <p class="text-sm text-gray-500">Controle a disponibilidade física das tendas da empresa</p>
            </div>
            <a href="{{ route('tendas.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm text-sm transition">
                + Nova Tenda
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 min-w-[640px]">
                    <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-700 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Tenda</th>
                            <th class="px-6 py-4">Tamanho</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4">Disponível no Estoque</th>
                            <th class="px-6 py-4">Valor Diária</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($tendas as $tenda)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $tenda->nome }}</div>
                                    <div class="text-xs text-gray-400">{{ $tenda->descricao }}</div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $tenda->tamanho }}</td>
                                <td class="px-6 py-4">{{ $tenda->quantidade_total }} un.</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $tenda->quantidade_disponivel > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $tenda->quantidade_disponivel }} disponíveis
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    R$ {{ number_format($tenda->valor_diaria, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('tendas.edit', $tenda) }}" class="text-xs text-blue-600 hover:text-blue-800 font-semibold">Editar</a>
                                    <form action="{{ route('tendas.destroy', $tenda) }}" method="POST" class="inline" onsubmit="return confirm('Excluir esta tenda?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-semibold">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Nenhuma tenda cadastrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

