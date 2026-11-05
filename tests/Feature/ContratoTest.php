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
            'telefone' => '17991429132',
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

    public function test_contrato_especifico_do_pedido_pega_valores_datas_tendas_e_quantidades_dinamicamente(): void
    {
        $tendaA = Tenda::create([
            'nome' => 'Tenda Piramidal 5x5m',
            'tamanho' => '5x5m',
            'quantidade_total' => 10,
            'quantidade_disponivel' => 10,
            'valor_diaria' => 150.00,
        ]);

        $tendaB = Tenda::create([
            'nome' => 'Tenda Piramidal Cristal 10x10m',
            'tamanho' => '10x10m',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'valor_diaria' => 600.00,
        ]);

        // Pedido 1
        $pedido1 = Pedido::create([
            'cliente_id' => $this->cliente->id,
            'data_instalacao' => '2026-11-10',
            'hora_instalacao' => '08:30:00',
            'data_retirada' => '2026-11-12',
            'hora_retirada' => '18:00:00',
            'endereco_instalacao' => 'Rua Alfa, 100',
            'cidade_instalacao' => 'São José do Rio Preto',
            'cep_instalacao' => '15010-000',
            'valor_total' => 750.00,
            'status' => 'pendente',
            'observacoes' => 'Montar na grama perto da piscina',
        ]);
        $pedido1->tendas()->attach($tendaA->id, ['quantidade' => 3]);

        // Pedido 2 (diferente valores, tendas, quantidades, datas e local)
        $pedido2 = Pedido::create([
            'cliente_id' => $this->cliente->id,
            'data_instalacao' => '2026-12-01',
            'hora_instalacao' => '14:00:00',
            'data_retirada' => '2026-12-05',
            'hora_retirada' => '10:00:00',
            'endereco_instalacao' => 'Fazenda Boa Vista, Km 15',
            'cidade_instalacao' => 'Mirassol',
            'cep_instalacao' => '15130-000',
            'valor_total' => 1800.00,
            'status' => 'os_gerada',
            'observacoes' => 'Ancoragem reforçada com cintas',
        ]);
        $pedido2->tendas()->attach($tendaB->id, ['quantidade' => 1]);

        $contratoController = app(\App\Http\Controllers\ContratoController::class);

        // Verifica dados do Pedido 1
        $dados1 = $contratoController->prepararDadosContrato($this->cliente, $pedido1);
        $this->assertEquals(sprintf('%04d', $pedido1->id), $dados1['numero_contrato']);
        $this->assertEquals('750,00', $dados1['valor_total']);
        $this->assertStringContainsString('reais', $dados1['valor_total_extenso']);
        $this->assertStringContainsString('10/11/2026 08:30', $dados1['saida']);
        $this->assertStringContainsString('12/11/2026 18:00', $dados1['retorno']);
        $this->assertStringContainsString('Rua Alfa, 100', $dados1['local_instalacao']);
        $this->assertCount(1, $dados1['itens']);
        $this->assertEquals('Tenda Piramidal 5x5m', $dados1['itens'][0]['nome']);
        $this->assertEquals(3, $dados1['itens'][0]['quantidade']);
        $this->assertEquals('Montar na grama perto da piscina', $dados1['observacoes']);

        // Verifica dados do Pedido 2
        $dados2 = $contratoController->prepararDadosContrato($this->cliente, $pedido2);
        $this->assertEquals(sprintf('%04d', $pedido2->id), $dados2['numero_contrato']);
        $this->assertEquals('1.800,00', $dados2['valor_total']);
        $this->assertStringContainsString('01/12/2026 14:00', $dados2['saida']);
        $this->assertStringContainsString('05/12/2026 10:00', $dados2['retorno']);
        $this->assertStringContainsString('Fazenda Boa Vista, Km 15', $dados2['local_instalacao']);
        $this->assertCount(1, $dados2['itens']);
        $this->assertEquals('Tenda Piramidal Cristal 10x10m', $dados2['itens'][0]['nome']);
        $this->assertEquals(1, $dados2['itens'][0]['quantidade']);
        $this->assertEquals('Ancoragem reforçada com cintas', $dados2['observacoes']);

        // Visualização das rotas do pedido específico
        $respVer1 = $this->actingAs($this->admin)->get(route('pedidos.contrato.ver', $pedido1));
        $respVer1->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $respVer1->headers->get('Content-Type'));

        $respDownload2 = $this->actingAs($this->admin)->get(route('pedidos.contrato.download', $pedido2));
        $respDownload2->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $respDownload2->headers->get('Content-Type'));

        // Geração do contrato para o pedido específico
        $respGerar = $this->actingAs($this->admin)->post(route('pedidos.contrato.gerar', $pedido1));
        $respGerar->assertStatus(200);
        $respGerar->assertJson(['success' => true]);
        Storage::disk('public')->assertExists("contratos/contrato_pedido_{$pedido1->id}.pdf");
    }

    public function test_contrato_sem_valor_informado_exibe_apenas_descricao_e_sem_valores(): void
    {
        $tendaA = Tenda::create([
            'nome' => 'Tenda Piramidal 10x10m',
            'tamanho' => '10x10m',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'valor_diaria' => 600.00,
        ]);

        $tendaB = Tenda::create([
            'nome' => 'Tenda Piramidal 5x5m',
            'tamanho' => '5x5m',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'valor_diaria' => 250.00,
        ]);

        $pedido = Pedido::create([
            'cliente_id' => $this->cliente->id,
            'data_instalacao' => '2026-11-10',
            'data_retirada' => '2026-11-12',
            'endereco_instalacao' => 'Rua dos Eventos, 500',
            'valor_total' => null, // Não informado
            'status' => 'pendente',
        ]);
        $pedido->tendas()->attach($tendaA->id, ['quantidade' => 2]);
        $pedido->tendas()->attach($tendaB->id, ['quantidade' => 1]);

        $contratoController = app(\App\Http\Controllers\ContratoController::class);
        $dados = $contratoController->prepararDadosContrato($this->cliente, $pedido);

        $this->assertFalse($dados['tem_valor']);
        $this->assertNull($dados['valor_total']);
        $this->assertNull($dados['valor_total_extenso']);
        $this->assertEquals(2, $dados['dias']);
        $this->assertCount(2, $dados['itens']);

        // Primeiro item (Tenda 10x10)
        $this->assertEquals('Tenda Piramidal 10x10m', $dados['itens'][0]['nome']);
        $this->assertEquals(2, $dados['itens'][0]['quantidade']);

        // Segundo item (Tenda 5x5)
        $this->assertEquals('Tenda Piramidal 5x5m', $dados['itens'][1]['nome']);
        $this->assertEquals(1, $dados['itens'][1]['quantidade']);

        // O pedido continua sem valor forçado no banco
        $pedido->refresh();
        $this->assertNull($pedido->valor_total);

        // Visualização em PDF não quebra e gera com sucesso
        $resp = $this->actingAs($this->admin)->get(route('pedidos.contrato.ver', $pedido));
        $resp->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $resp->headers->get('Content-Type'));
    }

    public function test_contrato_com_valor_fechado_pacote_faz_rateio_proporcional_exato_sem_diferenca(): void
    {
        $tendaA = Tenda::create([
            'nome' => 'Tenda Piramidal 10x10m',
            'tamanho' => '10x10m',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'valor_diaria' => 600.00,
        ]);

        $tendaB = Tenda::create([
            'nome' => 'Tenda Piramidal 5x5m',
            'tamanho' => '5x5m',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'valor_diaria' => 250.00,
        ]);

        $pedido = Pedido::create([
            'cliente_id' => $this->cliente->id,
            'data_instalacao' => '2026-11-10',
            'data_retirada' => '2026-11-12',
            'endereco_instalacao' => 'Rua dos Eventos, 500',
            'valor_total' => 2500.00,
            'status' => 'pendente',
        ]);
        $pedido->tendas()->attach($tendaA->id, ['quantidade' => 2]);
        $pedido->tendas()->attach($tendaB->id, ['quantidade' => 1]);

        $contratoController = app(\App\Http\Controllers\ContratoController::class);
        $dados = $contratoController->prepararDadosContrato($this->cliente, $pedido);

        $this->assertTrue($dados['tem_valor']);
        $this->assertEquals('2.500,00', $dados['valor_total']);
        $this->assertStringContainsString('dois mil e quinhentos reais', $dados['valor_total_extenso']);

        // A soma dos subtotais numéricos dos itens DEVE ser exatamente 2500.00
        $somaItens = 0.0;
        foreach ($dados['itens'] as $item) {
            $somaItens += $item['subtotal_numerico'];
        }
        $this->assertEquals(2500.00, round($somaItens, 2));

        // Rateio respeita a quantidade de peças (2 peças vs 1 peça)
        $this->assertGreaterThan($dados['itens'][1]['subtotal_numerico'], $dados['itens'][0]['subtotal_numerico']);
    }

    public function test_pedido_store_sem_valor_salva_nulo_sem_calcular_diaria(): void
    {
        $tenda = Tenda::create([
            'nome' => 'Tenda Sanfonada 3x3m',
            'tamanho' => '3x3m',
            'quantidade_total' => 10,
            'quantidade_disponivel' => 10,
            'valor_diaria' => 120.00,
        ]);

        $response = $this->actingAs($this->admin)->post(route('pedidos.store'), [
            'cliente_id' => $this->cliente->id,
            'data_instalacao' => '2026-11-01',
            'data_retirada' => '2026-11-04',
            'endereco_instalacao' => 'Av Central, 123',
            'valor_total' => '', // Vazio
            'tendas_selecionadas' => [$tenda->id],
            'quantidades' => [$tenda->id => 2],
        ]);

        $response->assertRedirect();

        $pedido = Pedido::latest()->first();
        $this->assertNotNull($pedido);
        $this->assertNull($pedido->valor_total);
    }

    public function test_pedido_store_com_valor_informado_salva_exatamente_o_valor(): void
    {
        $tenda = Tenda::create([
            'nome' => 'Tenda Sanfonada 3x3m',
            'tamanho' => '3x3m',
            'quantidade_total' => 10,
            'quantidade_disponivel' => 10,
            'valor_diaria' => 120.00,
        ]);

        $response = $this->actingAs($this->admin)->post(route('pedidos.store'), [
            'cliente_id' => $this->cliente->id,
            'data_instalacao' => '2026-11-01',
            'data_retirada' => '2026-11-04',
            'endereco_instalacao' => 'Av Central, 123',
            'valor_total' => '1.500,00',
            'tendas_selecionadas' => [$tenda->id],
            'quantidades' => [$tenda->id => 2],
        ]);

        $response->assertRedirect();

        $pedido = Pedido::latest()->first();
        $this->assertNotNull($pedido);
        $this->assertEquals(1500.00, (float) $pedido->valor_total);
    }

    public function test_montador_nao_tem_acesso_ao_contrato_e_recebe_403(): void
    {
        $montador = User::factory()->create([
            'role' => 'montador',
        ]);

        $pedido = Pedido::create([
            'cliente_id' => $this->cliente->id,
            'montador_id' => $montador->id,
            'data_instalacao' => '2026-11-10',
            'data_retirada' => '2026-11-12',
            'endereco_instalacao' => 'Rua dos Eventos, 500',
            'valor_total' => 1200.00,
            'status' => 'pendente',
        ]);

        // 1. Tentar visualizar contrato do pedido -> 403
        $respVer = $this->actingAs($montador)->get(route('pedidos.contrato.ver', $pedido));
        $respVer->assertStatus(403);

        // 2. Tentar baixar contrato do pedido -> 403
        $respDownload = $this->actingAs($montador)->get(route('pedidos.contrato.download', $pedido));
        $respDownload->assertStatus(403);

        // 3. Tentar gerar contrato do pedido -> 403
        $respGerar = $this->actingAs($montador)->post(route('pedidos.contrato.gerar', $pedido));
        $respGerar->assertStatus(403);

        // 4. Na tela de show do pedido, o montador não vê os botões nem o card do contrato
        $respShow = $this->actingAs($montador)->get(route('pedidos.show', $pedido));
        $respShow->assertStatus(200);
        $respShow->assertDontSee('Contrato do Pedido (PDF)');
        $respShow->assertDontSee('Visualizar Contrato (PDF)');
        $respShow->assertDontSee('Baixar Contrato em PDF');
    }
}

