<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">Clientes</h2>
                <p class="text-sm text-gray-500 dark:text-emerald-300/70">Gestão de contatos e endereços</p>
            </div>
            <a href="{{ route('clientes.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold rounded-lg shadow-sm text-sm transition">
                + Novo Cliente
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-[#031d14] rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c] overflow-hidden transition-colors">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-emerald-100/90 min-w-[640px]">
                        <thead class="bg-gray-50 dark:bg-[#02140e] text-xs uppercase font-semibold text-gray-700 dark:text-emerald-300 border-b border-gray-100 dark:border-[#083d2c]">
                            <tr>
                                <th class="px-6 py-4">Nome</th>
                                <th class="px-6 py-4">Telefone / WhatsApp</th>
                                <th class="px-6 py-4">Endereço Principal</th>
                                <th class="px-6 py-4">Locações</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#083d2c]">
                            @forelse($clientes as $cliente)
                                <tr class="hover:bg-gray-50 dark:hover:bg-[#063325] transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $cliente->nome }}</div>
                                        <div class="text-xs text-gray-400 dark:text-emerald-400/60">{{ $cliente->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-emerald-600 dark:text-amber-400">{{ $cliente->telefone }}</td>
                                    <td class="px-6 py-4 text-gray-700 dark:text-emerald-100">{{ $cliente->endereco }} {{ $cliente->cidade ? " - {$cliente->cidade}" : '' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-[#02140e] text-gray-800 dark:text-emerald-200 border border-transparent dark:border-emerald-700/40">
                                            {{ $cliente->pedidos_count }} pedidos
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('clientes.edit', $cliente) }}" class="text-xs text-emerald-700 dark:text-amber-400 hover:underline font-semibold">Editar</a>
                                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline" onsubmit="return confirm('Excluir este cliente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline font-semibold">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-emerald-300/60">Nenhum cliente cadastrado ainda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
