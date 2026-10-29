<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Clientes</h2>
                <p class="text-sm text-gray-500">Gestão de contatos e endereços</p>
            </div>
            <a href="{{ route('clientes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm text-sm transition">
                + Novo Cliente
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
                            <th class="px-6 py-4">Nome</th>
                            <th class="px-6 py-4">Telefone / WhatsApp</th>
                            <th class="px-6 py-4">Endereço Principal</th>
                            <th class="px-6 py-4">Locações</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($clientes as $cliente)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $cliente->nome }}</div>
                                    <div class="text-xs text-gray-400">{{ $cliente->email }}</div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-emerald-600">{{ $cliente->telefone }}</td>
                                <td class="px-6 py-4">{{ $cliente->endereco }} {{ $cliente->cidade ? " - {$cliente->cidade}" : '' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                        {{ $cliente->pedidos_count }} pedidos
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="text-xs text-blue-600 hover:text-blue-800 font-semibold">Editar</a>
                                    <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline" onsubmit="return confirm('Excluir este cliente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-semibold">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Nenhum cliente cadastrado ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

