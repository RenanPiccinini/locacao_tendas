<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">Novo Cliente</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('clientes.store') }}" method="POST" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nome Completo / Razão Social *</label>
                        <input type="text" name="nome" required class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Telefone / WhatsApp *</label>
                        <input type="text" name="telefone" placeholder="5551999999999" required class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">E-mail</label>
                        <input type="email" name="email" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">CPF / CNPJ</label>
                        <input type="text" name="cpf_cnpj" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Endereço Principal *</label>
                        <input type="text" name="endereco" required class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cidade</label>
                        <input type="text" name="cidade" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Observações</label>
                    <textarea name="observacoes" rows="2" class="w-full border-gray-300 rounded-lg text-sm"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('clientes.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold">Cancelar</a>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold">Salvar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

