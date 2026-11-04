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
}
