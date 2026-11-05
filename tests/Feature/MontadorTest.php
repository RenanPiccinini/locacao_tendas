<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MontadorTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_pode_cadastrar_novo_montador(): void
    {
        $response = $this->actingAs($this->admin)->post(route('montadores.store'), [
            'name' => 'Marcia Gibertoni',
            'email' => 'marciasabino@hotmail.com.br',
            'whatsapp' => '17991078924',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        $response->assertRedirect(route('montadores.index'));
        $response->assertSessionHas('success', 'Montador cadastrado com sucesso!');

        $this->assertDatabaseHas('users', [
            'name' => 'Marcia Gibertoni',
            'email' => 'marciasabino@hotmail.com.br',
            'whatsapp' => '17991078924',
            'role' => 'montador',
        ]);
    }

    public function test_validacao_de_senha_curta_retorna_mensagem_em_portugues(): void
    {
        $response = $this->actingAs($this->admin)->post(route('montadores.store'), [
            'name' => 'Marcia',
            'email' => 'marciasabino@hotmail.com.br',
            'whatsapp' => '17991078924',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors(['password']);
        
        $errors = session('errors')->get('password');
        $this->assertContains('A senha deve conter no mínimo 6 caracteres.', $errors);
        $this->assertNotContains('validation.min.string', $errors);
    }

    public function test_validacao_de_campos_obrigatorios_em_portugues(): void
    {
        $response = $this->actingAs($this->admin)->post(route('montadores.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'whatsapp', 'password']);

        $nameErrors = session('errors')->get('name');
        $this->assertContains('Informe o nome completo do montador.', $nameErrors);

        $emailErrors = session('errors')->get('email');
        $this->assertContains('Informe o e-mail de acesso ao sistema.', $emailErrors);

        $whatsappErrors = session('errors')->get('whatsapp');
        $this->assertContains('Informe o número de WhatsApp (com DDD).', $whatsappErrors);

        $passwordErrors = session('errors')->get('password');
        $this->assertContains('Informe uma senha provisória para o montador.', $passwordErrors);
    }

    public function test_validacao_de_confirmacao_de_senha_em_portugues(): void
    {
        $response = $this->actingAs($this->admin)->post(route('montadores.store'), [
            'name' => 'Marcia',
            'email' => 'marciasabino@hotmail.com.br',
            'whatsapp' => '17991078924',
            'password' => '123456',
            'password_confirmation' => '654321',
        ]);

        $response->assertSessionHasErrors(['password']);
        $passwordErrors = session('errors')->get('password');
        $this->assertContains('A confirmação de senha não confere.', $passwordErrors);
    }

    public function test_validacao_de_email_duplicado_em_portugues(): void
    {
        User::factory()->create([
            'email' => 'marciasabino@hotmail.com.br',
        ]);

        $response = $this->actingAs($this->admin)->post(route('montadores.store'), [
            'name' => 'Marcia 2',
            'email' => 'marciasabino@hotmail.com.br',
            'whatsapp' => '17991078924',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        $response->assertSessionHasErrors(['email']);
        $emailErrors = session('errors')->get('email');
        $this->assertContains('Este e-mail já está cadastrado no sistema.', $emailErrors);
    }
}

