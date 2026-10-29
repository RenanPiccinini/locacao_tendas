<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\OrdemServico;
use App\Models\Pedido;
use App\Models\Tenda;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Usuário Administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@locacaotendas.com.br'],
            [
                'name' => 'Administrador PDS',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'whatsapp' => '5551999999999',
            ]
        );

        // 2. Usuários Montadores de Exemplo
        $montador1 = User::firstOrCreate(
            ['email' => 'montador@locacaotendas.com.br'],
            [
                'name' => 'João Montador',
                'password' => Hash::make('password'),
                'role' => 'montador',
                'whatsapp' => '5551999999999',
            ]
        );

        $montador2 = User::firstOrCreate(
            ['email' => 'carlos@locacaotendas.com.br'],
            [
                'name' => 'Carlos Silva',
                'password' => Hash::make('password'),
                'role' => 'montador',
                'whatsapp' => '5551988888888',
            ]
        );

        // 3. Tendas para o Estoque
        $tenda1 = Tenda::firstOrCreate(
            ['nome' => 'Tenda Piramidal 5x5m'],
            [
                'tamanho' => '5x5m',
                'descricao' => 'Lona branca impermeável, estrutura tubular de ferro reforçado',
                'quantidade_total' => 6,
                'quantidade_disponivel' => 4,
                'valor_diaria' => 250.00,
            ]
        );

        $tenda2 = Tenda::firstOrCreate(
            ['nome' => 'Tenda Piramidal 10x10m'],
            [
                'tamanho' => '10x10m',
                'descricao' => 'Grande porte, ideal para eventos de até 150 pessoas',
                'quantidade_total' => 3,
                'quantidade_disponivel' => 2,
                'valor_diaria' => 650.00,
            ]
        );

        $tenda3 = Tenda::firstOrCreate(
            ['nome' => 'Tenda Sanfonada Articulada 3x3m'],
            [
                'tamanho' => '3x3m',
                'descricao' => 'Montagem rápida em alumínio, versátil para feiras e stands',
                'quantidade_total' => 8,
                'quantidade_disponivel' => 7,
                'valor_diaria' => 120.00,
            ]
        );

        // 4. Clientes de Exemplo
        $cliente1 = Cliente::firstOrCreate(
            ['telefone' => '5551991234567'],
            [
                'nome' => 'Espaço Bella Vista Eventos',
                'email' => 'contato@bellavista.com.br',
                'cpf_cnpj' => '12.345.678/0001-90',
                'endereco' => 'Av. das Palmeiras, 1500 - Bairro Jardim',
                'cidade' => 'Porto Alegre',
                'cep' => '90000-000',
                'observacoes' => 'Entrada lateral para caminhão de descarga',
            ]
        );

        $cliente2 = Cliente::firstOrCreate(
            ['telefone' => '5551987654321'],
            [
                'nome' => 'Marcos Vinícius (Casamento)',
                'email' => 'marcos.v@email.com',
                'cpf_cnpj' => '123.456.789-00',
                'endereco' => 'Rua dos Cravos, 220 - Chácara das Flores',
                'cidade' => 'Canoas',
                'cep' => '92000-000',
                'observacoes' => 'Terreno de grama nivelado',
            ]
        );

        // 5. Pedido de Exemplo com O.S.
        $hoje = Carbon::today();
        $pedido = Pedido::create([
            'cliente_id' => $cliente1->id,
            'montador_id' => $montador1->id,
            'data_instalacao' => $hoje,
            'hora_instalacao' => '09:00:00',
            'data_retirada' => $hoje->copy()->addDays(2),
            'hora_retirada' => '18:00:00',
            'endereco_instalacao' => 'Av. das Palmeiras, 1500 - Bairro Jardim, Porto Alegre',
            'cidade_instalacao' => 'Porto Alegre',
            'status' => 'os_gerada',
            'valor_total' => 500.00,
            'observacoes' => 'Montar tenda 5x5 perto da piscina com fixação reforçada.',
        ]);

        $pedido->tendas()->attach($tenda1->id, ['quantidade' => 2]);

        OrdemServico::create([
            'pedido_id' => $pedido->id,
            'tipo' => 'instalacao',
            'numero_os' => 'OSI-' . date('Y') . '-00001',
            'status' => 'pendente',
        ]);
    }
}
