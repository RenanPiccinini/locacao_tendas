<?php

namespace Tests\Feature;

use App\Services\WhatsappService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CronTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $mockWhatsapp = $this->createMock(WhatsappService::class);
        $mockWhatsapp->method('enviarMensagem')->willReturn(true);
        $this->app->instance(WhatsappService::class, $mockWhatsapp);
    }

    public function test_endpoint_web_cron_scheduler(): void
    {
        $response = $this->get(route('cron.scheduler'));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_endpoint_web_cron_lembretes(): void
    {
        $responseDia = $this->get(route('cron.lembretes', ['tipo' => 'dia']));
        $responseDia->assertStatus(200);
        $responseDia->assertJson(['success' => true, 'tipo' => 'dia']);

        $responseSemana = $this->get(route('cron.lembretes', ['tipo' => 'semana']));
        $responseSemana->assertStatus(200);
        $responseSemana->assertJson(['success' => true, 'tipo' => 'semana']);
    }

    public function test_horario_lembretes_e_textos_da_home_atualizados(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);

        $resWhatsapp = $this->actingAs($admin)->get(route('whatsapp.conexao'));
        $resWhatsapp->assertOk();
        $resWhatsapp->assertSee('Toda Segunda-feira às 07h00');
        $resWhatsapp->assertSee('Todos os dias às 07h00');
        $resWhatsapp->assertDontSee('às 08h00');

        $consoleContent = file_get_contents(base_path('routes/console.php'));
        $this->assertStringContainsString("weeklyOn(1, '07:00')", $consoleContent);
        $this->assertStringContainsString("dailyAt('07:00')", $consoleContent);

        $resHome = $this->get(route('home'));
        $resHome->assertOk();
        $resHome->assertSee('Tenda 3x3m');
        $resHome->assertDontSee('Tenda 3x3m (Simples ou c/ Balcão)');
        $resHome->assertDontSee('Fechamentos laterais retráteis com trilho e zíper de vedação.');
        $resHome->assertDontSee('Tenda Piramidal c/ Fechamento');
        $resHome->assertDontSee('Ver Mais Fotos no Instagram @tendaseldorado');
    }
}

