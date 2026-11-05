<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $montador;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'ativo' => true,
        ]);

        $this->montador = User::factory()->create([
            'role' => 'montador',
            'ativo' => true,
        ]);
    }

    public function test_apenas_admin_pode_acessar_gestao_de_usuarios(): void
    {
        // Montador tenta acessar e recebe 403
        $responseMontador = $this->actingAs($this->montador)->get(route('usuarios.index'));
        $responseMontador->assertForbidden();

        // Admin acessa com sucesso
        $responseAdmin = $this->actingAs($this->admin)->get(route('usuarios.index'));
        $responseAdmin->assertOk();
        $responseAdmin->assertSee('Gestão de Usuários');
    }

    public function test_admin_pode_cadastrar_novo_usuario_admin_ou_montador(): void
    {
        $response = $this->actingAs($this->admin)->post(route('usuarios.store'), [
            'name' => 'Novo Administrador',
            'email' => 'novo.admin@eldorado.com',
            'whatsapp' => '17999998888',
            'role' => 'admin',
            'ativo' => '1',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Novo Administrador',
            'email' => 'novo.admin@eldorado.com',
            'role' => 'admin',
            'ativo' => true,
        ]);
    }

    public function test_admin_pode_bloquear_e_desbloquear_usuario(): void
    {
        $alvo = User::factory()->create([
            'role' => 'montador',
            'ativo' => true,
        ]);

        // Bloquear
        $resBloqueio = $this->actingAs($this->admin)
            ->from(route('usuarios.index'))
            ->post(route('usuarios.toggle-bloqueio', $alvo));
        $resBloqueio->assertRedirect(route('usuarios.index'));
        $this->assertFalse($alvo->fresh()->isAtivo());

        // Liberar
        $resLiberar = $this->actingAs($this->admin)
            ->from(route('usuarios.index'))
            ->post(route('usuarios.toggle-bloqueio', $alvo));
        $resLiberar->assertRedirect(route('usuarios.index'));
        $this->assertTrue($alvo->fresh()->isAtivo());
    }

    public function test_admin_nao_pode_bloquear_a_si_mesmo(): void
    {
        $response = $this->actingAs($this->admin)
            ->from(route('usuarios.index'))
            ->post(route('usuarios.toggle-bloqueio', $this->admin));
        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('error');
        $this->assertTrue($this->admin->fresh()->isAtivo());
    }

    public function test_usuario_bloqueado_nao_consegue_fazer_login(): void
    {
        $usuarioBloqueado = User::factory()->create([
            'email' => 'bloqueado@eldorado.com',
            'password' => Hash::make('senha123'),
            'ativo' => false,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'bloqueado@eldorado.com',
            'password' => 'senha123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_pode_alterar_senha_de_usuario(): void
    {
        $outroAdmin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('senhaAntiga123'),
        ]);

        $response = $this->actingAs($this->admin)
            ->from(route('usuarios.index'))
            ->post(route('usuarios.alterar-senha', $outroAdmin), [
                'password' => 'novasenha456',
                'password_confirmation' => 'novasenha456',
            ]);

        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('novasenha456', $outroAdmin->fresh()->password));
    }

    public function test_admin_pode_excluir_usuario_mas_nao_a_si_mesmo(): void
    {
        $alvo = User::factory()->create(['role' => 'montador']);

        // Excluir outro usuário
        $res = $this->actingAs($this->admin)
            ->from(route('usuarios.index'))
            ->delete(route('usuarios.destroy', $alvo));
        $res->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseMissing('users', ['id' => $alvo->id]);

        // Tentar excluir a si próprio
        $resSelf = $this->actingAs($this->admin)
            ->from(route('usuarios.index'))
            ->delete(route('usuarios.destroy', $this->admin));
        $resSelf->assertRedirect(route('usuarios.index'));
        $resSelf->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }
}

