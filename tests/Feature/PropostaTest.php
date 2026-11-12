<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Proposta;
use App\Models\Tenda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropostaTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_nao_autenticado_e_redirecionado(): void
    {
        $this->get(route('propostas.index'))->assertRedirect(route('login'));
        $this->get(route('propostas.create'))->assertRedirect(route('login'));
    }

    public function test_admin_e_montador_visualizam_menu_propostas_e_acessam_listagem(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $montador = User::factory()->create(['role' => 'montador']);

        $resAdmin = $this->actingAs($admin)->get(route('dashboard'));
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('Propostas');
        $resAdmin->assertSee(route('propostas.index'));

        $resMontador = $this->actingAs($montador)->get(route('dashboard'));
        $resMontador->assertStatus(200);
        $resMontador->assertSee('Propostas');
        $resMontador->assertSee(route('propostas.index'));

        $this->actingAs($admin)->get(route('propostas.index'))->assertStatus(200);
        $this->actingAs($montador)->get(route('propostas.index'))->assertStatus(200);
        $this->actingAs($montador)->get(route('propostas.create'))->assertStatus(200);
    }

    public function test_criar_proposta_completa_e_gerar_pdf_identico_ao_modelo(): void
    {
        $montador = User::factory()->create([
            'name' => 'Carlos Consultor',
            'role' => 'montador',
            'whatsapp' => '17991429132',
        ]);

        $cliente = Cliente::create([
            'nome' => 'MUNICIPIO DE MIRASSOL',
            'telefone' => '17991447381',
            'cpf_cnpj' => '46.612.032/0001-49',
            'cep' => '15130065',
            'endereco' => 'PRACA DOUTOR ANISIO JOSE MOREIRA',
            'numero' => '2290',
            'bairro' => 'CENTRO',
            'cidade' => 'MIRASSOL',
            'estado' => 'SP',
        ]);

        $tenda = Tenda::create([
            'nome' => 'Tenda Piramidal 10x10m',
            'tamanho' => '10x10m',
            'quantidade_total' => 4,
            'quantidade_disponivel' => 4,
            'valor_diaria' => 500.00,
        ]);

        $payload = [
            'numero' => '88',
            'cliente_id' => $cliente->id,
            'data_proposta' => '2026-11-10',
            'identificacao' => 'MULHERES EMPREENDEDORA NATAL',
            'status' => 'enviada',

            // De:
            'de_razao_social' => '58.950.200 MARCIA REGINA SABINO GIBERTONI',
            'de_fantasia' => 'TENDAS ELDORADO',
            'de_cnpj' => '58.950.200/0001-95',
            'de_ie' => 'ISENTO',
            'de_im' => '12345',
            'de_cep' => '15077429',
            'de_endereco' => 'Rua Ruth Tranjan Donegá, 11',
            'de_bairro' => 'Parque Residencial Buona Vita',
            'de_cidade' => 'São José do Rio Preto',
            'de_uf' => 'SP',
            'de_consultor' => 'Carlos Consultor',
            'de_celular' => '17991429132',
            'de_email' => 'contato@tendaseldorado.com.br',

            // Para:
            'para_razao_social' => 'MUNICIPIO DE MIRASSOL',
            'para_fantasia' => 'Mirassol Prefeitura Municipal',
            'para_cnpj_cpf' => '46.612.032/0001-49',
            'para_ie' => 'ISENTO',
            'para_cep' => '15130065',
            'para_endereco' => 'PRACA DOUTOR ANISIO JOSE MOREIRA, 2290',
            'para_bairro' => 'CENTRO',
            'para_cidade' => 'MIRASSOL',
            'para_uf' => 'SP',
            'para_solicitante' => 'SAMARA',
            'para_celular' => '17991447381',
            'para_email' => 'samara@mirassol.sp.gov.br',

            // Período(s)
            'data_saida' => '2026-12-07',
            'hora_saida' => '08:00',
            'data_inicio' => '2026-12-09',
            'hora_inicio' => '08:00',
            'data_fim' => '2026-12-09',
            'hora_fim' => '08:00',
            'data_retorno' => '2026-12-24',
            'hora_retorno' => '08:00',

            // Produtos
            'itens' => [
                [
                    'tenda_id' => $tenda->id,
                    'descricao' => 'TENDA - 10 X 10 metros',
                    'quantidade' => 2,
                    'valor_periodo' => '1.250,00',
                ],
            ],

            // Totais (formato brasileiro)
            'valor_total' => '2.500,00',
            'valor_desconto' => '150,00',
            'valor_total_geral' => '2.350,00',

            'pagamentos' => '50% na assinatura e 50% após a instalação.',
            'observacoes' => 'Inclui montagem, ancoragem e desmontagem completa.',
        ];

        $response = $this->actingAs($montador)->post(route('propostas.store'), $payload);

        $proposta = Proposta::with('itens')->first();
        $this->assertNotNull($proposta);
        $response->assertRedirect(route('propostas.show', $proposta));

        $this->assertEquals('88', $proposta->numero);
        $this->assertEquals($montador->id, $proposta->user_id);
        $this->assertEquals(2500.00, (float) $proposta->valor_total);
        $this->assertEquals(150.00, (float) $proposta->valor_desconto);
        $this->assertEquals(2350.00, (float) $proposta->valor_total_geral);
        $this->assertCount(1, $proposta->itens);
        $this->assertEquals('TENDA - 10 X 10 metros', $proposta->itens->first()->descricao);
        $this->assertEquals(2, $proposta->itens->first()->quantidade);
        $this->assertEquals(1250.00, (float) $proposta->itens->first()->valor_periodo);

        // Testa view de detalhes
        $this->actingAs($montador)
            ->get(route('propostas.show', $proposta))
            ->assertStatus(200)
            ->assertSee('Proposta de Locação Nº 88')
            ->assertSee('MUNICIPIO DE MIRASSOL');

        // Testa stream e download de PDF
        $resPdf = $this->actingAs($montador)->get(route('propostas.pdf', $proposta));
        $resPdf->assertStatus(200);
        $resPdf->assertHeader('content-type', 'application/pdf');

        $resDownload = $this->actingAs($montador)->get(route('propostas.download', $proposta));
        $resDownload->assertStatus(200);
        $resDownload->assertHeader('content-type', 'application/pdf');

        // Valida layout e dados exatos renderizados no Blade do PDF
        $html = view('pdf.proposta', ['proposta' => $proposta])->render();

        $this->assertStringContainsString('PROPOSTA DE LOCAÇÃO Nº 88', $html);
        $this->assertStringContainsString('10 de novembro de 2026', $html);
        $this->assertStringContainsString('MULHERES EMPREENDEDORA NATAL', $html);
        $this->assertStringContainsString('De:', $html);
        $this->assertStringContainsString('58.950.200 MARCIA REGINA SABINO GIBERTONI', $html);
        $this->assertStringContainsString('TENDAS ELDORADO', $html);
        $this->assertStringContainsString('58.950.200/0001-95', $html);
        $this->assertStringContainsString('Consultor:', $html);
        $this->assertStringContainsString('Carlos Consultor', $html);
        $this->assertStringContainsString('Para:', $html);
        $this->assertStringContainsString('MUNICIPIO DE MIRASSOL', $html);
        $this->assertStringContainsString('Mirassol Prefeitura Municipal', $html);
        $this->assertStringContainsString('46.612.032/0001-49', $html);
        $this->assertStringContainsString('Inscrição Estadual:', $html);
        $this->assertStringContainsString('SAMARA', $html);
        $this->assertStringContainsString('17991447381', $html);
        $this->assertStringContainsString('Data do Evento', $html);
        $this->assertStringContainsString('<strong>De:</strong> 09/12/2026', $html);
        $this->assertStringContainsString('<strong>Até:</strong> 09/12/2026', $html);
        $this->assertStringContainsString('Produto(s)', $html);
        $this->assertStringContainsString('Qtd', $html);
        $this->assertStringContainsString('Valor/Período', $html);
        $this->assertStringContainsString('TENDA - 10 X 10 metros', $html);
        $this->assertStringContainsString('R$ 1.250,00', $html);
        $this->assertStringNotContainsString('Total Desconto:', $html);
        $this->assertStringContainsString('<strong>Total Geral:</strong> R$ 2.350,00', $html);
        $this->assertStringContainsString('Pagamento(s)', $html);
        $this->assertStringContainsString('50% na assinatura e 50% após a instalação.', $html);
        $this->assertStringContainsString('Observações:', $html);
        $this->assertStringContainsString('Inclui montagem, ancoragem e desmontagem completa.', $html);
        $this->assertStringContainsString('DATA', $html);
        $this->assertStringContainsString('10/11/2026', $html);
        $this->assertStringContainsString('ASSINATURA', $html);
        $this->assertStringContainsString('Validade da proposta: 30 dias', $html);
    }

    public function test_admin_edita_atualiza_calcula_totais_e_exclui_proposta(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('propostas.store'), [
            'numero' => '90',
            'data_proposta' => '2026-11-10',
            'identificacao' => 'EVENTO INICIAL',
            'de_razao_social' => '58.950.200 MARCIA REGINA SABINO GIBERTONI',
            'para_razao_social' => 'EMPRESA CLIENTE LTDA',
            'itens' => [
                [
                    'descricao' => 'TENDA 5X5M',
                    'quantidade' => 3,
                    'valor_periodo' => '300,00',
                ],
            ],
            'valor_desconto' => '100,00',
        ]);

        $proposta = Proposta::first();
        $this->assertNotNull($proposta);
        // Auto-calculou 3 * 300 = 900 e total_geral = 900 - 100 = 800
        $this->assertEquals(900.00, (float) $proposta->valor_total);
        $this->assertEquals(800.00, (float) $proposta->valor_total_geral);

        $resEdit = $this->actingAs($admin)->get(route('propostas.edit', $proposta));
        $resEdit->assertStatus(200);

        $resUpdate = $this->actingAs($admin)->put(route('propostas.update', $proposta), [
            'numero' => '90',
            'data_proposta' => '2026-11-11',
            'identificacao' => 'EVENTO ATUALIZADO',
            'status' => 'aprovada',
            'de_razao_social' => '58.950.200 MARCIA REGINA SABINO GIBERTONI',
            'para_razao_social' => 'EMPRESA CLIENTE LTDA',
            'itens' => [],
            'valor_total' => '0,00',
            'valor_desconto' => '0,00',
            'valor_total_geral' => '0,00',
        ]);

        $resUpdate->assertRedirect(route('propostas.show', $proposta));

        $proposta->refresh();
        $this->assertEquals('EVENTO ATUALIZADO', $proposta->identificacao);
        $this->assertEquals('aprovada', $proposta->status);
        $this->assertCount(0, $proposta->itens);

        // Busca na listagem
        $this->actingAs($admin)
            ->get(route('propostas.index', ['busca' => 'ATUALIZADO']))
            ->assertStatus(200)
            ->assertSee('EMPRESA CLIENTE LTDA');

        // Exclusão
        $resDelete = $this->actingAs($admin)->delete(route('propostas.destroy', $proposta));
        $resDelete->assertRedirect(route('propostas.index'));
        $this->assertDatabaseMissing('propostas', ['id' => $proposta->id]);
    }

    public function test_proposta_pdf_oculta_campos_nao_preenchidos_usa_novo_endereco_e_exibe_assinatura_acima_do_pontilhado(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $dadosPadrao = Proposta::dadosPadraoLocadora($admin);
        $this->assertEquals('Rua Pedro Martins, 190', $dadosPadrao['de_endereco']);
        $this->assertEquals('Distrito Industrial Adail Vetorasso', $dadosPadrao['de_bairro']);
        $this->assertEquals('15046-773', $dadosPadrao['de_cep']);

        $this->actingAs($admin)->post(route('propostas.store'), [
            'numero' => '101',
            'data_proposta' => '2026-11-12',
            'de_razao_social' => $dadosPadrao['de_razao_social'],
            'de_fantasia' => $dadosPadrao['de_fantasia'],
            'de_cnpj' => $dadosPadrao['de_cnpj'],
            'de_ie' => $dadosPadrao['de_ie'],
            'de_im' => '',
            'de_cep' => $dadosPadrao['de_cep'],
            'de_endereco' => $dadosPadrao['de_endereco'],
            'de_bairro' => $dadosPadrao['de_bairro'],
            'de_cidade' => $dadosPadrao['de_cidade'],
            'de_uf' => $dadosPadrao['de_uf'],
            'para_razao_social' => 'CARLA SILVA',
            'itens' => [
                [
                    'descricao' => 'Tenda Piramidal 3 x 3m',
                    'quantidade' => 1,
                    'valor_periodo' => '100,00',
                ],
            ],
            'valor_total_geral' => '100,00',
        ]);

        $proposta = Proposta::where('numero', '101')->firstOrFail();
        $html = view('pdf.proposta', ['proposta' => $proposta])->render();

        // Novo endereço De:
        $this->assertStringContainsString('Rua Pedro Martins, 190', $html);
        $this->assertStringContainsString('Distrito Industrial Adail Vetorasso', $html);
        $this->assertStringContainsString('15046-773', $html);

        // Campos preenchidos aparecem
        $this->assertStringContainsString('CARLA SILVA', $html);
        $this->assertStringContainsString('Tenda Piramidal 3 x 3m', $html);
        $this->assertStringContainsString('R$ 100,00', $html);

        // Campos não preenchidos NÃO devem aparecer
        $this->assertStringNotContainsString('Identificação:', $html);
        $this->assertStringNotContainsString('IM:', $html);
        $this->assertSame(1, substr_count($html, 'Fantasia:')); // apenas em "De:", oculto em "Para:"
        $this->assertStringNotContainsString('Inscrição Estadual:', $html);
        $this->assertStringNotContainsString('Solicitante:', $html);
        $this->assertStringNotContainsString('Data do Evento', $html);
        $this->assertStringNotContainsString('Pagamento(s)', $html);
        $this->assertStringNotContainsString('Observações:', $html);

        // Assinatura da Marcia acima da linha pontilhada (antes de sig-line e ASSINATURA)
        $posAssinaturaMarcia = strpos($html, 'Marcia Regina Sabino Gibertoni');
        $posUltimaSigLine = strrpos($html, 'class="sig-line"');
        $posLabelAssinatura = strpos($html, '<div class="sig-label">ASSINATURA</div>');
        $this->assertNotFalse($posAssinaturaMarcia);
        $this->assertNotFalse($posUltimaSigLine);
        $this->assertNotFalse($posLabelAssinatura);
        $this->assertLessThan($posUltimaSigLine, $posAssinaturaMarcia);
        $this->assertLessThan($posLabelAssinatura, $posUltimaSigLine);
    }
}
