<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Tenda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_autenticado_pode_abrir_calendario(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('calendario.index'));

        $response->assertOk();
        $response->assertSee('Calendário de Instalações e Retiradas');
        $response->assertSee('Ver Pedido Completo');
    }

    public function test_eventos_do_calendario_retornam_urls_e_dados_completos(): void
    {
        $montador = User::factory()->create([
            'name' => 'João Montador',
            'role' => 'montador',
        ]);

        $cliente = Cliente::create([
            'nome' => 'Espaço Bella Vista Eventos',
            'telefone' => '17991112233',
            'endereco' => 'Rua das Palmeiras, 100',
        ]);

        $tenda = Tenda::create([
            'nome' => 'Tenda Piramidal 5x5m',
            'tamanho' => '5x5m',
            'quantidade_total' => 2,
            'quantidade_disponivel' => 2,
            'valor_diaria' => 120,
        ]);

        $pedido = Pedido::create([
            'cliente_id' => $cliente->id,
            'montador_id' => $montador->id,
            'data_instalacao' => now()->format('Y-m-d'),
            'hora_instalacao' => '09:00',
            'data_retirada' => now()->addDays(2)->format('Y-m-d'),
            'hora_retirada' => '17:00',
            'endereco_instalacao' => 'teste, 99',
            'status' => 'os_gerada',
        ]);
        $pedido->tendas()->attach($tenda->id, ['quantidade' => 1]);

        $response = $this->actingAs($montador)->getJson(route('calendario.eventos'));
        $response->assertOk();

        $eventos = $response->json();
        $this->assertNotEmpty($eventos);
        $this->assertCount(2, $eventos); // 1 montagem + 1 retirada

        foreach ($eventos as $evento) {
            $this->assertArrayHasKey('url', $evento);
            $this->assertEquals(route('pedidos.show', $pedido), $evento['url']);

            $this->assertArrayHasKey('extendedProps', $evento);
            $props = $evento['extendedProps'];

            // Garante que pedido_url e cliente_nome existem e não são indefinidos
            $this->assertEquals(route('pedidos.show', $pedido), $props['pedido_url']);
            $this->assertEquals('Espaço Bella Vista Eventos', $props['cliente_nome']);
            $this->assertEquals('João Montador', $props['montador_nome']);
            $this->assertNotEmpty($props['tipo']);
            $this->assertNotEmpty($props['tipo_label']);
        }
    }
}
