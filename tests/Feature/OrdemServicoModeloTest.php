<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\OrdemServico;
use App\Models\Pedido;
use App\Models\Tenda;
use App\Models\User;
use App\Services\WhatsappService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdemServicoModeloTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $mockWhatsapp = $this->createMock(WhatsappService::class);
        $mockWhatsapp->method('enviarMensagem')->willReturn(true);
        $this->app->instance(WhatsappService::class, $mockWhatsapp);
    }

    public function test_novo_pedido_com_evento_e_responsavel_e_pdf_identico(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $montador = User::factory()->create([
            'name' => 'Montador Equipe A',
            'role' => 'montador',
            'whatsapp' => '17991112222',
        ]);

        $cliente = Cliente::create([
            'nome' => 'MUNICIPIO DE TANABI',
            'nome_fantasia' => 'Tanabi Gabinete Prefeito',
            'telefone' => '17997000000',
            'endereco' => 'Rua Central, 50',
            'cidade' => 'Tanabi',
            'estado' => 'SP',
        ]);

        $tenda1 = Tenda::create([
            'nome' => 'Tenda Piramidal 10x10',
            'tamanho' => '10 x 10 metros',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'valor_diaria' => 200,
        ]);

        $tenda2 = Tenda::create([
            'nome' => 'Tenda Piramidal 5x5',
            'tamanho' => '5 x 5 metros',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'valor_diaria' => 100,
        ]);

        // Cria o pedido com data do evento e dados do responsável
        $response = $this->actingAs($admin)->post(route('pedidos.store'), [
            'cliente_id' => $cliente->id,
            'montador_id' => $montador->id,
            'data_instalacao' => '2026-10-30',
            'hora_instalacao' => '08:00',
            'data_evento' => '2026-10-31',
            'data_retirada' => '2026-11-03',
            'hora_retirada' => '08:00',
            'endereco_instalacao' => 'Cemitério Jardim da Igualdade',
            'cidade_instalacao' => 'Tanabi',
            'observacoes' => 'FINADOS TANABI 02/11/2026',
            'nome_responsavel' => 'LUCAS',
            'telefone_responsavel' => '17997260201',
            'tendas_selecionadas' => [$tenda1->id, $tenda2->id],
            'quantidades' => [$tenda1->id => 2, $tenda2->id => 1],
            'gerar_os_agora' => '1',
        ]);

        $response->assertSessionHas('success');

        $pedido = Pedido::first();
        $this->assertNotNull($pedido);
        $this->assertEquals('LUCAS', $pedido->nome_responsavel);
        $this->assertEquals('17997260201', $pedido->telefone_responsavel);
        $this->assertEquals('2026-10-31', $pedido->data_evento->format('Y-m-d'));

        // Verifica a O.S. gerada
        $os = $pedido->ordensServico()->first();
        $this->assertNotNull($os);

        // Testa download da O.S. e geração do PDF
        $resPdf = $this->actingAs($admin)->get(route('os.download', $os));
        $resPdf->assertStatus(200);
        $resPdf->assertHeader('content-type', 'application/pdf');

        // Testa stream da O.S.
        $resStream = $this->actingAs($admin)->get(route('os.pdf', $os));
        $resStream->assertStatus(200);
        $resStream->assertHeader('content-type', 'application/pdf');

        // Renderiza a view diretamente para validar strings exatas do modelo
        $viewHtml = view('pdf.ordem_servico', [
            'os' => $os,
            'pedido' => $pedido->load(['cliente', 'montador', 'tendas']),
        ])->render();

        $this->assertStringContainsString('ORDEM DE SERVIÇO Nº', $viewHtml);
        $this->assertStringContainsString('FINADOS TANABI 02/11/2026', $viewHtml);
        $this->assertStringContainsString('MUNICIPIO DE TANABI', $viewHtml);
        $this->assertStringContainsString('LUCAS', $viewHtml);
        $this->assertStringContainsString('17997260201', $viewHtml);
        $this->assertStringContainsString('Saída/Retorno', $viewHtml);
        $this->assertStringContainsString('Cemitério Jardim da Igualdade', $viewHtml);
        $this->assertStringContainsString('Período(s)', $viewHtml);
        $this->assertStringContainsString('Data do Evento:', $viewHtml);
        $this->assertStringContainsString('31/10/2026', $viewHtml);
        $this->assertStringContainsString('30/10/2026', $viewHtml);
        $this->assertStringContainsString('03/11/2026', $viewHtml);
        $this->assertStringContainsString('Produto(s)', $viewHtml);
        $this->assertStringContainsString('Quantidade', $viewHtml);
        $this->assertStringContainsString('2 PEÇAS', $viewHtml);
        $this->assertStringContainsString('1 PEÇA', $viewHtml);
        $this->assertStringContainsString('Observações:', $viewHtml);
    }

    public function test_geracao_automatica_de_os_nos_tres_momentos_e_retorno_ao_estoque(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $montador = User::factory()->create(['role' => 'montador', 'whatsapp' => '17991429132']);

        $cliente = Cliente::create([
            'nome' => 'Empresa Teste OS Automática',
            'telefone' => '17991429132',
            'endereco' => 'Av. Brasil, 100',
        ]);

        $tenda = Tenda::create([
            'nome' => 'Tenda 10x10 Piramidal',
            'tamanho' => '10x10',
            'quantidade_total' => 10,
            'quantidade_disponivel' => 10,
            'valor_diaria' => 150.00,
        ]);

        // MOMENTO 1: Cria o pedido -> Gera a 1ª OS automaticamente
        $resStore = $this->actingAs($admin)->post(route('pedidos.store'), [
            'cliente_id' => $cliente->id,
            'montador_id' => $montador->id,
            'data_instalacao' => now()->format('Y-m-d'),
            'hora_instalacao' => '08:00',
            'data_retirada' => now()->addDays(3)->format('Y-m-d'),
            'hora_retirada' => '18:00',
            'endereco_instalacao' => 'Rua das Flores, 500',
            'tendas_selecionadas' => [$tenda->id],
            'quantidades' => [$tenda->id => 3],
        ]);

        $resStore->assertSessionHas('success');
        $pedido = Pedido::first();
        $this->assertNotNull($pedido);
        $this->assertEquals(7, $tenda->fresh()->quantidade_disponivel); // Deduziu 3 do estoque
        $this->assertCount(1, $pedido->fresh()->ordensServico); // 1ª OS gerada
        $primeiraOs = $pedido->fresh()->ordensServico->first();
        $this->assertEquals('instalacao', $primeiraOs->tipo);

        // MOMENTO 2: Montador clica no botão e pega para instalar -> Gera outra OS automaticamente
        $resIniciar = $this->actingAs($montador)->post(route('pedidos.iniciar-instalacao', $pedido));
        $resIniciar->assertSessionHas('success');
        $this->assertEquals('em_instalacao', $pedido->fresh()->status);
        $this->assertCount(2, $pedido->fresh()->ordensServico); // 2ª OS gerada automaticamente

        // MOMENTO 3: Montador clica no botão e faz a devolução -> Devolve tendas ao estoque e gera outra OS
        $resDevolver = $this->actingAs($montador)->post(route('pedidos.confirmar-retirada', $pedido));
        $resDevolver->assertSessionHas('success');
        $this->assertEquals('concluido', $pedido->fresh()->status);
        $this->assertEquals(10, $tenda->fresh()->quantidade_disponivel); // Tendas devolvidas ao estoque!
        $this->assertCount(3, $pedido->fresh()->ordensServico); // 3ª OS (devolução) gerada automaticamente

        // Verifica que todas as OSs ficam disponíveis dinamicamente no pedido
        $resShow = $this->actingAs($admin)->get(route('pedidos.show', $pedido));
        $resShow->assertStatus(200);
        foreach ($pedido->fresh()->ordensServico as $osItem) {
            $resShow->assertSee($osItem->numero_os);
        }
    }

    public function test_rotas_de_atalho_para_pc_abrem_direto_no_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Testa download do atalho HTML
        $resHtml = $this->actingAs($admin)->get(route('atalho.pc'));
        $resHtml->assertStatus(200);
        $resHtml->assertHeader('content-disposition', 'attachment; filename="Tendas Eldorado - Painel.html"');
        $this->assertStringContainsString('/dashboard', $resHtml->getContent());

        // Testa download do atalho .url
        $resUrl = $this->actingAs($admin)->get(route('atalho.url'));
        $resUrl->assertStatus(200);
        $resUrl->assertHeader('content-disposition', 'attachment; filename="Tendas Eldorado - Painel.url"');
        $this->assertStringContainsString('/dashboard', $resUrl->getContent());
    }
}
