<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\OrdemServico;
use App\Models\Pedido;
use App\Models\Tenda;
use App\Models\User;
use App\Services\WhatsappService;
use Carbon\Carbon;
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

        // Verifica que exatamente 3 ordens de serviço foram geradas automaticamente no ciclo completo (1ª na criação, 2ª na conclusão da instalação, 3ª na devolução)
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
        $montadorA = User::factory()->create([
            'role' => 'montador',
            'name' => 'Montador Carlos',
            'whatsapp' => '17991111111',
        ]);
        $montadorB = User::factory()->create([
            'role' => 'montador',
            'name' => 'Montador Sem Pedidos',
            'whatsapp' => '17992222222',
        ]);
        $cliente = Cliente::create([
            'nome' => 'Cliente Eventos',
            'telefone' => '17993333333',
            'endereco' => 'Rua Principal, 50',
        ]);
        $tenda = Tenda::create([
            'nome' => 'Tenda Piramidal 10x10',
            'tamanho' => '10x10m',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'valor_diaria' => 200,
        ]);

        $hoje = Carbon::now('America/Sao_Paulo')->format('Y-m-d');

        $pedido = Pedido::create([
            'cliente_id' => $cliente->id,
            'montador_id' => $montadorA->id,
            'data_instalacao' => $hoje,
            'data_retirada' => Carbon::now('America/Sao_Paulo')->addDays(1)->format('Y-m-d'),
            'endereco_instalacao' => 'Rua Principal, 50',
            'status' => 'os_gerada',
        ]);
        $pedido->tendas()->attach($tenda->id, ['quantidade' => 2]);

        $mensagens = [];
        $mockWpp = $this->createMock(WhatsappService::class);
        $mockWpp->method('enviarMensagem')
            ->willReturnCallback(function ($numero, $msg) use (&$mensagens) {
                $mensagens[] = ['numero' => $numero, 'msg' => $msg];
                return true;
            });
        $this->app->instance(WhatsappService::class, $mockWpp);

        // 1. Testa disparo diário
        $this->artisan('lembretes:whatsapp', ['--tipo' => 'dia'])->assertExitCode(0);

        // Deve enviar apenas para o Montador A (que tem pedido hoje), e NUNCA para o Montador B
        $this->assertCount(1, $mensagens);
        $this->assertEquals('17991111111', $mensagens[0]['numero']);
        $this->assertStringContainsString('AGENDA DE HOJE', $mensagens[0]['msg']);
        $this->assertStringContainsString('Tenda Piramidal 10x10', $mensagens[0]['msg']);

        // 2. Testa disparo semanal
        $mensagens = [];
        $this->artisan('lembretes:whatsapp', ['--tipo' => 'semana'])->assertExitCode(0);

        $this->assertCount(1, $mensagens);
        $this->assertEquals('17991111111', $mensagens[0]['numero']);
        $this->assertStringContainsString('AGENDA DA SEMANA', $mensagens[0]['msg']);

        // 3. Testa trava anti-repetição: segunda execução NÃO deve re-enviar
        $mensagensRepetidas = [];
        $this->artisan('lembretes:whatsapp', ['--tipo' => 'dia'])->assertExitCode(0);
        $this->artisan('lembretes:whatsapp', ['--tipo' => 'semana'])->assertExitCode(0);
        $this->assertCount(0, $mensagensRepetidas, 'Lembretes não devem ser repetidos no mesmo dia ou semana.');
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

    public function test_regras_whatsapp_e_os_tres_casos_e_dois_botoes_montador(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'whatsapp' => '17999991111',
        ]);
        $montador = User::factory()->create([
            'role' => 'montador',
            'whatsapp' => '17988882222',
        ]);
        $cliente = Cliente::create([
            'nome' => 'Cliente Teste WhatsApp',
            'telefone' => '1732438120',
            'endereco' => 'Av Brasil, 100',
        ]);
        $tenda = Tenda::create([
            'nome' => 'Tenda Piramidal 5x5',
            'tamanho' => '5x5m',
            'quantidade_total' => 10,
            'quantidade_disponivel' => 10,
            'valor_diaria' => 100,
        ]);

        $mensagensEnviadas = [];
        $mockWhatsapp = $this->createMock(WhatsappService::class);
        $mockWhatsapp->method('enviarMensagem')
            ->willReturnCallback(function ($numero, $msg) use (&$mensagensEnviadas) {
                $mensagensEnviadas[] = ['numero' => $numero, 'msg' => $msg];
                return true;
            });
        $mockWhatsapp->method('getNumeroPrincipal')->willReturn('17999991111');
        $mockWhatsapp->method('notificarWhatsappPrincipal')
            ->willReturnCallback(function ($msg) use (&$mensagensEnviadas) {
                $mensagensEnviadas[] = ['numero' => '17999991111', 'msg' => $msg];
                return true;
            });
        $this->app->instance(WhatsappService::class, $mockWhatsapp);

        // 1º CASO: Criação do Pedido -> Gera 1ª OS e notifica o MONTADOR no WhatsApp
        $this->actingAs($admin)->post(route('pedidos.store'), [
            'cliente_id' => $cliente->id,
            'montador_id' => $montador->id,
            'data_instalacao' => now()->format('Y-m-d'),
            'hora_instalacao' => '09:00',
            'data_retirada' => now()->addDays(2)->format('Y-m-d'),
            'hora_retirada' => '17:00',
            'endereco_instalacao' => 'Av Brasil, 100',
            'tendas_selecionadas' => [$tenda->id],
            'quantidades' => [$tenda->id => 1],
        ]);

        $pedido = Pedido::latest()->first();
        $this->assertNotNull($pedido);
        $this->assertCount(1, $pedido->ordensServico); // 1ª OS gerada
        $this->assertCount(1, $mensagensEnviadas);
        $this->assertEquals('17988882222', $mensagensEnviadas[0]['numero']); // Montador recebeu a OS inicial!
        $this->assertStringContainsString('ORDEM DE SERVIÇO EMITIDA', $mensagensEnviadas[0]['msg']);

        // Verifica os botões na tela do pedido: só deve aparecer "Tenda instalada"
        $respShow1 = $this->actingAs($montador)->get(route('pedidos.show', $pedido));
        $respShow1->assertSee('Tenda instalada');
        $respShow1->assertDontSee('Estou em Processo de Instalação');
        $respShow1->assertDontSee('A Caminho da Retirada');

        // 2º CASO: Montador conclui instalação ("Tenda instalada")
        // -> Emite a 2ª OS, Montador NÃO recebe aviso, WhatsApp PRINCIPAL recebe aviso de instalação!
        $mensagensEnviadas = [];
        $this->actingAs($montador)->post(route('pedidos.finalizar-instalacao', $pedido), [
            'observacoes_montador' => 'Montado com sucesso!',
        ]);

        $pedido->refresh();
        $this->assertEquals('aguardando_retirada', $pedido->status);
        $this->assertCount(2, $pedido->ordensServico); // Exatamente 2 OS até aqui!
        $this->assertCount(1, $mensagensEnviadas);
        $this->assertEquals('17999991111', $mensagensEnviadas[0]['numero']); // Foi para o WhatsApp PRINCIPAL!
        $this->assertStringContainsString('TENDA INSTALADA COM SUCESSO', $mensagensEnviadas[0]['msg']);

        // Verifica os botões na tela do pedido: só deve aparecer "Tenda retirada"
        $respShow2 = $this->actingAs($montador)->get(route('pedidos.show', $pedido));
        $respShow2->assertSee('Tenda retirada');
        $respShow2->assertDontSee('<span>Tenda instalada</span>', false);
        $respShow2->assertDontSee('A Caminho da Retirada');

        // 3º CASO: Montador conclui retirada ("Tenda retirada")
        // -> Emite a 3ª OS, tendas retornam ao estoque, Montador NÃO recebe aviso, WhatsApp PRINCIPAL recebe aviso de retirada!
        $mensagensEnviadas = [];
        $this->actingAs($montador)->post(route('pedidos.confirmar-retirada', $pedido));

        $pedido->refresh();
        $this->assertEquals('concluido', $pedido->status);
        $this->assertCount(3, $pedido->ordensServico); // Exatamente 3 OS no total!
        $this->assertEquals(10, $tenda->fresh()->quantidade_disponivel); // Estoque retornado!
        $this->assertCount(1, $mensagensEnviadas);
        $this->assertEquals('17999991111', $mensagensEnviadas[0]['numero']); // Foi para o WhatsApp PRINCIPAL!
        $this->assertStringContainsString('TENDA RETIRADA & ESTOQUE ATUALIZADO', $mensagensEnviadas[0]['msg']);
    }

    public function test_criar_pedido_com_gerar_os_desmarcado_nao_notifica_montador_na_criacao(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $montador = User::factory()->create(['role' => 'montador', 'whatsapp' => '17998887766']);
        $cliente = Cliente::create(['nome' => 'Cliente Futuro', 'telefone' => '17997776655', 'endereco' => 'Avenida Principal, 100']);
        $tenda = Tenda::create([
            'nome' => 'Tenda 5x5m',
            'tamanho' => '5x5',
            'quantidade_total' => 4,
            'quantidade_disponivel' => 4,
            'valor_diaria' => 200.00,
        ]);

        $mensagensEnviadas = [];
        $mockWpp = $this->createMock(WhatsappService::class);
        $mockWpp->method('enviarMensagem')
            ->willReturnCallback(function ($numero, $msg) use (&$mensagensEnviadas) {
                $mensagensEnviadas[] = ['numero' => $numero, 'msg' => $msg];
                return true;
            });
        $this->app->instance(WhatsappService::class, $mockWpp);

        // Cria pedido para a próxima semana com gerar_os_agora DESMARCADO
        $this->actingAs($admin)->post(route('pedidos.store'), [
            'cliente_id' => $cliente->id,
            'montador_id' => $montador->id,
            'data_instalacao' => now()->addDays(7)->format('Y-m-d'),
            'data_retirada' => now()->addDays(9)->format('Y-m-d'),
            'endereco_instalacao' => 'Avenida Principal, 100',
            'tendas_selecionadas' => [$tenda->id],
            'quantidades' => [$tenda->id => 1],
            'tem_campo_gerar_os' => '1',
            'gerar_os_agora' => '0', // Desmarcado
        ]);

        $pedido = Pedido::where('cliente_id', $cliente->id)->first();
        $this->assertNotNull($pedido);
        // Montador NÃO deve ter recebido nenhuma notificação no momento do cadastro!
        $this->assertCount(0, $mensagensEnviadas, 'Montador não pode receber WhatsApp na criação quando a opção estiver desmarcada.');
    }

    public function test_listagem_pedidos_ordena_por_data_instalacao_e_exibe_botao_novo_pedido_dentro_do_pedido(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $clienteA = Cliente::create(['nome' => 'Cliente Instalacao Dia 10', 'telefone' => '17991110001', 'endereco' => 'Rua A, 1']);
        $clienteB = Cliente::create(['nome' => 'Cliente Instalacao Dia 20', 'telefone' => '17991110002', 'endereco' => 'Rua B, 2']);
        $clienteC = Cliente::create(['nome' => 'Cliente Instalacao Dia 15', 'telefone' => '17991110003', 'endereco' => 'Rua C, 3']);

        // Criados fora de ordem cronológica de instalação
        $pedidoDia10 = Pedido::create([
            'cliente_id' => $clienteA->id,
            'data_instalacao' => '2026-11-10',
            'hora_instalacao' => '08:00',
            'data_retirada' => '2026-11-12',
            'endereco_instalacao' => 'Rua A, 1',
            'status' => 'os_gerada',
        ]);
        $pedidoDia20 = Pedido::create([
            'cliente_id' => $clienteB->id,
            'data_instalacao' => '2026-11-20',
            'hora_instalacao' => '14:00',
            'data_retirada' => '2026-11-22',
            'endereco_instalacao' => 'Rua B, 2',
            'status' => 'os_gerada',
        ]);
        $pedidoDia15 = Pedido::create([
            'cliente_id' => $clienteC->id,
            'data_instalacao' => '2026-11-15',
            'hora_instalacao' => '10:00',
            'data_retirada' => '2026-11-17',
            'endereco_instalacao' => 'Rua C, 3',
            'status' => 'os_gerada',
        ]);

        // Listagem padrão: ordenada por data de instalação (ascendente, igual ao Dashboard: 10/11, 15/11, 20/11)
        $resAsc = $this->actingAs($admin)->get(route('pedidos.index'));
        $resAsc->assertStatus(200);
        $resAsc->assertSeeInOrder([
            'Cliente Instalacao Dia 10',
            'Cliente Instalacao Dia 15',
            'Cliente Instalacao Dia 20',
        ]);

        // Listagem descendente via toggle
        $resDesc = $this->actingAs($admin)->get(route('pedidos.index', ['ordem' => 'desc']));
        $resDesc->assertStatus(200);
        $resDesc->assertSeeInOrder([
            'Cliente Instalacao Dia 20',
            'Cliente Instalacao Dia 15',
            'Cliente Instalacao Dia 10',
        ]);

        // Dentro do pedido (show e edit): botão "Novo Pedido de Locação" apontando para pedidos.create
        $resShow = $this->actingAs($admin)->get(route('pedidos.show', $pedidoDia10));
        $resShow->assertStatus(200);
        $resShow->assertSee('Novo Pedido de Locação');
        $resShow->assertSee(route('pedidos.create'));

        $resEdit = $this->actingAs($admin)->get(route('pedidos.edit', $pedidoDia10));
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Novo Pedido de Locação');
        $resEdit->assertSee(route('pedidos.create'));
    }
}

