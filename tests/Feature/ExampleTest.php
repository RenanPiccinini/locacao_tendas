<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se a página inicial do site carrega com sucesso e exibe as 3 imagens reais e o showcase.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Tendas Eldorado');
        $response->assertSee('images/1.jpeg');
        $response->assertSee('images/2.jpeg');
        $response->assertSee('images/3.jpeg');
        $response->assertSee('Fotos Reais Tendas Eldorado');
        $response->assertSee('Galeria de Fotos Reais');
    }

    /**
     * Testa se o layout do painel contém o banner do celular e o banner/botão do computador.
     */
    public function test_dashboard_renders_mobile_and_desktop_pwa_banners(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('pwa-install-banner');
        $response->assertSee('btn-instalar-app');
        $response->assertSee('desktop-install-banner');
        $response->assertSee('btn-desktop-shortcut');
        $response->assertSee('modal-desktop-shortcut');
        $response->assertSee('modal-ios-pwa');
    }
}

