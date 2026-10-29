<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">Editar Cliente: {{ $cliente->nome }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('clientes.update', $cliente) }}" method="POST" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nome Completo *</label>
                        <input type="text" name="nome" value="{{ old('nome', $cliente->nome) }}" required class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Telefone / WhatsApp *</label>
                        <input type="text" name="telefone" value="{{ old('telefone', $cliente->telefone) }}" required class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">E-mail</label>
                        <input type="email" name="email" value="{{ old('email', $cliente->email) }}" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">CPF / CNPJ</label>
                        <input type="text" name="cpf_cnpj" value="{{ old('cpf_cnpj', $cliente->cpf_cnpj) }}" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Endereço Principal *</label>
                        <input type="text" name="endereco" value="{{ old('endereco', $cliente->endereco) }}" required class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cidade</label>
                        <input type="text" name="cidade" value="{{ old('cidade', $cliente->cidade) }}" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Observações</label>
                    <textarea name="observacoes" rows="2" class="w-full border-gray-300 rounded-lg text-sm">{{ old('observacoes', $cliente->observacoes) }}</textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('clientes.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold">Cancelar</a>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

