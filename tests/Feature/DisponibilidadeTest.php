<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Tenda;
use App\Models\User;
use App\Services\DisponibilidadeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisponibilidadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_calcula_disponibilidade_e_retorno_corretamente(): void
    {
        // 1. Cria tendas
        $tendaA = Tenda::create([
            'nome' => 'Tenda Piramidal 10x10',
            'tamanho' => '10x10',
            'quantidade_total' => 10,
            'quantidade_disponivel' => 10,
            'valor_diaria' => 250.00,
        ]);

        $tendaB = Tenda::create([
            'nome' => 'Tenda Sanfonada 3x3',
            'tamanho' => '3x3',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'valor_diaria' => 100.00,
        ]);

        // 2. Cria cliente e montador
        $cliente = Cliente::create([
            'nome' => 'Carlos Silva',
            'telefone' => '51999998888',
            'endereco' => 'Av Central, 100',
        ]);

        $montador = User::factory()->create([
            'role' => 'montador',
        ]);

        // 3. Cria pedido ativo com 3 tendas A e 2 tendas B (14/11/2026 a 16/11/2026, retorno às 17h)
        $pedido = Pedido::create([
            'cliente_id' => $cliente->id,
            'montador_id' => $montador->id,
            'data_instalacao' => '2026-11-14',
            'hora_instalacao' => '09:00:00',
            'data_retirada' => '2026-11-16',
            'hora_retirada' => '17:00:00',
            'endereco_instalacao' => 'Av Central, 100',
            'status' => 'instalado',
            'valor_total' => 750.00,
        ]);

        $pedido->tendas()->attach($tendaA->id, ['quantidade' => 3]);
        $pedido->tendas()->attach($tendaB->id, ['quantidade' => 2]);

        // 4. Cria outro pedido que está cancelado (não deve impactar a disponibilidade)
        $pedidoCancelado = Pedido::create([
            'cliente_id' => $cliente->id,
            'montador_id' => $montador->id,
            'data_instalacao' => '2026-11-14',
            'data_retirada' => '2026-11-16',
            'endereco_instalacao' => 'Rua Cancelada, 50',
            'status' => 'cancelado',
            'valor_total' => 500.00,
        ]);
        $pedidoCancelado->tendas()->attach($tendaA->id, ['quantidade' => 5]);

        $service = app(DisponibilidadeService::class);

        // Consulta para 15/11/2026
        $resultado15 = $service->consultarDisponibilidade('2026-11-15', '2026-11-15');

        $this->assertEquals(15, $resultado15['total_tendas_estoque']);
        $this->assertEquals(5, $resultado15['total_em_locacao']);
        $this->assertEquals(10, $resultado15['total_disponivel']);
        $this->assertCount(2, $resultado15['locacoes']); // 1 para tenda A e 1 para tenda B

        $tendaAResult = $resultado15['tendas']->firstWhere('id', $tendaA->id);
        $this->assertEquals(3, $tendaAResult->quantidade_em_locacao);
        $this->assertEquals(7, $tendaAResult->quantidade_disponivel_periodo);

        $tendaBResult = $resultado15['tendas']->firstWhere('id', $tendaB->id);
        $this->assertEquals(2, $tendaBResult->quantidade_em_locacao);
        $this->assertEquals(3, $tendaBResult->quantidade_disponivel_periodo);

        $locacao = $resultado15['locacoes']->first();
        $this->assertEquals('2026-11-16', $locacao['data_retirada']->format('Y-m-d'));
        $this->assertEquals('17:00:00', $locacao['hora_retirada']);

        // Consulta para período 10/11/2026 até 12/11/2026 (antes da instalação)
        $resultadoAntes = $service->consultarDisponibilidade('2026-11-10', '2026-11-12');
        $this->assertEquals(0, $resultadoAntes['total_em_locacao']);
        $this->assertEquals(15, $resultadoAntes['total_disponivel']);

        // Consulta para período 20/11/2026 até 25/11/2026 (após a retirada)
        $resultadoDepois = $service->consultarDisponibilidade('2026-11-20', '2026-11-25');
        $this->assertEquals(0, $resultadoDepois['total_em_locacao']);
        $this->assertEquals(15, $resultadoDepois['total_disponivel']);
    }
}
