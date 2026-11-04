<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_pode_ver_listagem_de_clientes(): void
    {
        Cliente::create([
            'nome' => 'Espaço Bella Vista',
            'telefone' => '17991429132',
            'cep' => '15046-773',
            'endereco' => 'Rua Pedro Martins',
            'numero' => '190',
            'bairro' => 'Eldorado',
            'cidade' => 'São José do Rio Preto',
            'estado' => 'SP',
        ]);

        $response = $this->actingAs($this->admin)->get(route('clientes.index'));

        $response->assertStatus(200);
        $response->assertSee('Espaço Bella Vista');
        $response->assertSee('Rua Pedro Martins, 190');
        $response->assertSee('Eldorado');
    }

    public function test_admin_pode_cadastrar_cliente_com_campos_separados_de_endereco(): void
    {
        $dados = [
            'nome' => 'Renan Piccinini',
            'telefone' => '17991429132',
            'email' => 'renan@exemplo.com',
            'cpf_cnpj' => '123.456.789-00',
            'cep' => '15046-773',
            'endereco' => 'Rua Pedro Martins',
            'numero' => '190',
            'complemento' => 'Sala 2',
            'bairro' => 'Eldorado',
            'cidade' => 'São José do Rio Preto',
            'estado' => 'sp',
            'observacoes' => 'Entregar no portão lateral',
        ];

        $response = $this->actingAs($this->admin)->post(route('clientes.store'), $dados);

        $response->assertRedirect(route('clientes.index'));
        $this->assertDatabaseHas('clientes', [
            'nome' => 'Renan Piccinini',
            'cep' => '15046-773',
            'endereco' => 'Rua Pedro Martins',
            'numero' => '190',
            'complemento' => 'Sala 2',
            'bairro' => 'Eldorado',
            'cidade' => 'São José do Rio Preto',
            'estado' => 'SP', // uppercase
        ]);

        $cliente = Cliente::where('nome', 'Renan Piccinini')->first();
        $this->assertEquals('Rua Pedro Martins', $cliente->rua);
        $this->assertStringContainsString('Rua Pedro Martins, nº 190', $cliente->endereco_completo);
        $this->assertStringContainsString('CEP: 15046-773', $cliente->endereco_completo);
    }

    public function test_admin_pode_atualizar_cliente(): void
    {
        $cliente = Cliente::create([
            'nome' => 'Cliente Original',
            'telefone' => '17999999999',
            'endereco' => 'Rua Antiga',
            'cidade' => 'Rio Preto',
        ]);

        $dados = [
            'nome' => 'Cliente Atualizado',
            'telefone' => '17988888888',
            'email' => 'cliente@atualizado.com',
            'cep' => '15000-000',
            'endereco' => 'Avenida Nova',
            'numero' => '500',
            'complemento' => 'Apto 12',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'sp',
        ];

        $response = $this->actingAs($this->admin)->put(route('clientes.update', $cliente), $dados);

        $response->assertRedirect(route('clientes.index'));
        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nome' => 'Cliente Atualizado',
            'endereco' => 'Avenida Nova',
            'numero' => '500',
            'bairro' => 'Centro',
            'estado' => 'SP',
        ]);
    }

    public function test_rota_consulta_cep_valida_tamanho_e_retorna_dados(): void
    {
        Http::fake([
            'https://viacep.com.br/ws/15046773/json/' => Http::response([
                'cep' => '15046-773',
                'logradouro' => 'Rua Pedro Martins',
                'complemento' => '',
                'bairro' => 'Eldorado',
                'localidade' => 'São José do Rio Preto',
                'uf' => 'SP',
                'ibge' => '3549805',
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)->get(route('cep.consulta', ['cep' => '15046-773']));

        $response->assertStatus(200);
        $response->assertJson([
            'logradouro' => 'Rua Pedro Martins',
            'bairro' => 'Eldorado',
            'localidade' => 'São José do Rio Preto',
            'uf' => 'SP',
        ]);
    }

    public function test_rota_consulta_cep_rejeita_cep_invalido(): void
    {
        $response = $this->actingAs($this->admin)->get(route('cep.consulta', ['cep' => '123']));

        $response->assertStatus(422);
        $response->assertJson([
            'erro' => true,
        ]);
    }
}
