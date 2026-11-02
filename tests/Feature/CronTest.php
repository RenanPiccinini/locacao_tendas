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
}

