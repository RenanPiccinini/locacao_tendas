<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Tenda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContratoTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Cliente $cliente;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->cliente = Cliente::create([
            'nome' => 'Michel Chostite',
            'telefone' => '17991555335',
            'email' => 'michel@email.com',
            'cpf_cnpj' => '123.456.789-00',
            'endereco' => 'Rua das Palmeiras, 1500',
            'cidade' => 'São José do Rio Preto',
            'cep' => '15000-000',
        ]);
    }

    public function test_gerar_contrato_para_cliente_sem_pedido(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('clientes.contrato.gerar', $this->cliente));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->cliente->refresh();
        $this->assertTrue($this->cliente->temContrato());
        $this->assertNotNull($this->cliente->contrato_path);
        Storage::disk('public')->assertExists($this->cliente->contrato_path);
    }

    public function test_gerar_contrato_para_cliente_com_pedido_e_tendas(): void
    {
        $tenda = Tenda::create([
            'nome' => 'Tenda Piramidal 5x5m',
            'tamanho' => '5x5m',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'valor_diaria' => 250.00,
        ]);

        $pedido = Pedido::create([
            'cliente_id' => $this->cliente->id,
            'data_instalacao' => now()->format('Y-m-d'),
            'hora_instalacao' => '09:00',
            'data_retirada' => now()->addDays(2)->format('Y-m-d'),
            'hora_retirada' => '17:00',
            'endereco_instalacao' => 'Chácara dos Pinhais, 100',
            'cidade_instalacao' => 'São José do Rio Preto',
            'valor_total' => 500.00,
            'status' => 'pendente',
        ]);

        $pedido->tendas()->attach($tenda->id, ['quantidade' => 2]);

        $response = $this->actingAs($this->admin)
            ->post(route('clientes.contrato.gerar', $this->cliente));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->cliente->refresh();
        $this->assertTrue($this->cliente->temContrato());
    }

    public function test_visualizar_contrato_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('clientes.contrato.ver', $this->cliente));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_baixar_contrato_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('clientes.contrato.download', $this->cliente));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_listagem_de_clientes_mostra_botoes_de_contrato(): void
    {
        // Antes de gerar contrato: botão Gerar Contrato
        $response = $this->actingAs($this->admin)
            ->get(route('clientes.index'));

        $response->assertStatus(200);
        $response->assertSee('Gerar Contrato');

        // Após gerar contrato: botão Ver Contrato
        $this->actingAs($this->admin)
            ->post(route('clientes.contrato.gerar', $this->cliente));

        $responseAfter = $this->actingAs($this->admin)
            ->get(route('clientes.index'));

        $responseAfter->assertStatus(200);
        $responseAfter->assertSee('Ver Contrato');
    }
}
