<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\OrdemServico;
use App\Models\Pedido;
use App\Models\Tenda;
use App\Models\User;
use App\Services\WhatsappService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FluxoMontadorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock do WhatsappService para não disparar chamadas HTTP reais nos testes
        $mockWhatsapp = $this->createMock(WhatsappService::class);
        $mockWhatsapp->method('enviarMensagem')->willReturn(true);
        $this->app->instance(WhatsappService::class, $mockWhatsapp);
    }

    public function test_ciclo_completo_pedido_montador_e_estoque(): void
    {
        Storage::fake('public');

        // 1. Cria admin, montador, cliente e tenda com estoque 5
        $admin = User::factory()->create(['role' => 'admin']);
        $montador = User::factory()->create([
            'role' => 'montador',
            'whatsapp' => '51988887777',
        ]);

        $cliente = Cliente::create([
            'nome' => 'Maria Silva',
            'telefone' => '51999990000',
            'endereco' => 'Rua das Flores, 123',
        ]);

        $tenda = Tenda::create([
            'nome' => 'Tenda 5x5 Piramidal',
            'tamanho' => '5x5',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'valor_diaria' => 150.00,
        ]);

        // 2. Cria pedido informando o montador
        // Deve gerar OS automaticamente, decrementar estoque e definir status os_gerada
        $response = $this->actingAs($admin)->post(route('pedidos.store'), [
            'cliente_id' => $cliente->id,
            'montador_id' => $montador->id,
            'data_instalacao' => now()->format('Y-m-d'),
            'hora_instalacao' => '09:00',
            'data_retirada' => now()->addDays(2)->format('Y-m-d'),
            'hora_retirada' => '17:00',
            'endereco_instalacao' => 'Rua das Flores, 123',
            'tendas_selecionadas' => [$tenda->id],
            'quantidades' => [$tenda->id => 2],
        ]);

        $pedido = Pedido::first();
        $this->assertNotNull($pedido);
        $this->assertEquals('os_gerada', $pedido->status);
        $this->assertEquals(3, $tenda->fresh()->quantidade_disponivel);

        $osInstalacao = $pedido->osInstalacao;
        $this->assertNotNull($osInstalacao);
        $this->assertEquals('instalacao', $osInstalacao->tipo);

        // 3. Montador clica no botão "Estou em Processo de Instalação"
        $this->actingAs($montador)->post(route('pedidos.iniciar-instalacao', $pedido));
        $this->assertEquals('em_instalacao', $pedido->fresh()->status);
        $this->assertEquals('em_andamento', $osInstalacao->fresh()->status);

        // 4. Montador anexa fotos durante o processo de montagem
        $foto1 = UploadedFile::fake()->image('montagem1.jpg');
        $this->actingAs($montador)->post(route('pedidos.anexar-fotos', $pedido), [
            'fotos' => [$foto1],
            'observacoes_montador' => 'Tenda ancorada no gramado.',
        ]);

        $this->assertCount(1, $osInstalacao->fresh()->fotos);
        $this->assertEquals('Tenda ancorada no gramado.', $osInstalacao->fresh()->observacoes_montador);

        // Verifica renderização da tela de pedido com fotos e lightbox
        $responseShow = $this->actingAs($admin)->get(route('pedidos.show', $pedido));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Fotos da Instalação / Conclusão');
        $responseShow->assertSee('openLightbox');
        $responseShow->assertSee('Clique em qualquer foto para ampliar na tela');

        // 5. Montador finaliza a instalação
        // Deve mudar status para "aguardando_retirada" e gerar nova OS de Retorno automaticamente
        $this->actingAs($montador)->post(route('pedidos.finalizar-instalacao', $pedido), [
            'observacoes_montador' => 'Instalação 100% concluída.',
        ]);

        $this->assertEquals('aguardando_retirada', $pedido->fresh()->status);

        $osRetirada = $pedido->fresh()->osRetirada;
        $this->assertNotNull($osRetirada);
        $this->assertEquals('retirada', $osRetirada->tipo);

        // 6. Montador clica em retirar / confirma retirada
        // Tendas devem voltar automaticamente para o estoque, status fica concluido e gera OS de Conclusao
        $this->actingAs($montador)->post(route('pedidos.confirmar-retirada', $pedido));

        $this->assertEquals('concluido', $pedido->fresh()->status);
        $this->assertEquals('concluida', $osRetirada->fresh()->status);
        $this->assertEquals(5, $tenda->fresh()->quantidade_disponivel); // Estoque devolvido!

        // Verifica que gerou também a OS de Conclusão respectiva ao status concluído
        $osConclusao = $pedido->fresh()->osConclusao;
        $this->assertNotNull($osConclusao);
        $this->assertEquals('conclusao', $osConclusao->tipo);
        $this->assertEquals('concluida', $osConclusao->status);

        // Verifica que todas as 3 ordens de serviço ficam disponíveis no pedido
        $this->assertCount(3, $pedido->fresh()->ordensServico);

        // 7. Testa gerar uma O.S. a mais manualmente respectiva ao pedido e status
        $this->actingAs($admin)->post(route('pedidos.gerar-os', $pedido), [
            'tipo' => 'manutencao',
        ]);
        $this->assertCount(4, $pedido->fresh()->ordensServico);

        // 8. Testa reenvio de O.S. via WhatsApp e download em PDF
        $resWpp = $this->actingAs($admin)->post(route('os.enviar-whatsapp', $osInstalacao));
        $resWpp->assertSessionHas('success');

        $resDown = $this->actingAs($admin)->get(route('os.download', $osInstalacao));
        $resDown->assertStatus(200);
    }

    public function test_comandos_lembrete_whatsapp_executam_com_sucesso(): void
    {
        $this->artisan('lembretes:whatsapp', ['--tipo' => 'semana'])->assertExitCode(0);
        $this->artisan('lembretes:whatsapp', ['--tipo' => 'dia'])->assertExitCode(0);
    }

    public function test_servir_arquivo_storage_e_exibicao_fotos_com_lightbox(): void
    {
        Storage::fake('public');

        // Cria arquivo fake no storage public
        Storage::disk('public')->put('fotos_instalacao/foto_teste.jpg', 'conteudo-imagem-binario');

        // Testa entrega via rota storage-file
        $response1 = $this->get(route('storage.file', ['path' => 'fotos_instalacao/foto_teste.jpg']));
        $response1->assertStatus(200);

        // Testa entrega via rota /storage/{path}
        $response2 = $this->get('/storage/fotos_instalacao/foto_teste.jpg');
        $response2->assertStatus(200);

        // Testa proteção contra path traversal
        $responseTraversal = $this->get('/storage-file/../../../etc/passwd');
        $this->assertContains($responseTraversal->status(), [403, 404]);

        // Testa endpoint de storage-link
        $responseLink = $this->get(route('cron.storage-link'));
        $responseLink->assertStatus(200);
        $responseLink->assertJson(['success' => true]);
    }
}

