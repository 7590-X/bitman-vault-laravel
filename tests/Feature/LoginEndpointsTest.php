<?php

namespace Tests\Feature;

use App\Infrastructure\Modelos\LoginModelo;
use App\Infrastructure\Modelos\UsuarioModelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class LoginEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private UsuarioModelo $usuario;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuario = UsuarioModelo::create([
            'nombre_completo'    => 'Usuario Pruebas',
            'correo_electronico' => 'test@example.com',
            'hash_contrasena'    => 'hash_ficticio',
            'sal_contrasena'     => 'sal_ficticia',
            'estado'             => 'activo',
        ]);

        $this->token = JWTAuth::fromUser($this->usuario);
    }

    public function test_no_permite_acceso_sin_token_jwt(): void
    {
        $response = $this->getJson('/api/logins');
        $response->assertStatus(401);
    }

    public function test_crear_login_exitosamente(): void
    {
        $payload = [
            'nombre_sitio'          => 'GitHub',
            'url'                   => 'https://github.com',
            'usuario_login'         => 'gituser',
            'contrasena_encriptada' => 'encrypted_password_base64',
            'notas'                 => 'Nota secreta',
        ];

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/logins', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('mensaje', 'Login registrado exitosamente.')
            ->assertJsonPath('datos.nombre_sitio', 'GitHub')
            ->assertJsonPath('datos.usuario_id', $this->usuario->id);

        $this->assertDatabaseHas('secretos.logins', [
            'usuario_id'   => $this->usuario->id,
            'nombre_sitio' => 'GitHub',
        ]);
    }

    public function test_obtener_solo_logins_del_usuario_autenticado(): void
    {
        // Login del usuario actual
        LoginModelo::create([
            'usuario_id'            => $this->usuario->id,
            'nombre_sitio'          => 'Mi Sitio',
            'contrasena_encriptada' => 'pass123',
        ]);

        // Usuario secundario y su login
        $otroUsuario = UsuarioModelo::create([
            'nombre_completo'    => 'Otro Usuario',
            'correo_electronico' => 'otro@example.com',
            'hash_contrasena'    => 'hash',
            'sal_contrasena'     => 'sal',
            'estado'             => 'activo',
        ]);

        LoginModelo::create([
            'usuario_id'            => $otroUsuario->id,
            'nombre_sitio'          => 'Sitio Ajeno',
            'contrasena_encriptada' => 'pass456',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/logins');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'datos')
            ->assertJsonPath('datos.0.nombre_sitio', 'Mi Sitio');
    }

    public function test_actualizar_login_propio(): void
    {
        $login = LoginModelo::create([
            'usuario_id'            => $this->usuario->id,
            'nombre_sitio'          => 'Antiguo Sitio',
            'contrasena_encriptada' => 'old_pass',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/logins/{$login->id}", [
                'nombre_sitio'          => 'Nuevo Sitio',
                'contrasena_encriptada' => 'new_pass',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('datos.nombre_sitio', 'Nuevo Sitio');

        $this->assertDatabaseHas('secretos.logins', [
            'id'           => $login->id,
            'nombre_sitio' => 'Nuevo Sitio',
        ]);
    }

    public function test_no_permite_actualizar_login_de_otro_usuario(): void
    {
        $otroUsuario = UsuarioModelo::create([
            'nombre_completo'    => 'Otro Usuario',
            'correo_electronico' => 'otro2@example.com',
            'hash_contrasena'    => 'hash',
            'sal_contrasena'     => 'sal',
            'estado'             => 'activo',
        ]);

        $loginAjeno = LoginModelo::create([
            'usuario_id'            => $otroUsuario->id,
            'nombre_sitio'          => 'Sitio Ajeno',
            'contrasena_encriptada' => 'pass',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/logins/{$loginAjeno->id}", [
                'nombre_sitio'          => 'Hackeado',
                'contrasena_encriptada' => 'hacked',
            ]);

        $response->assertStatus(403);
    }

    public function test_eliminar_login_propio(): void
    {
        $login = LoginModelo::create([
            'usuario_id'            => $this->usuario->id,
            'nombre_sitio'          => 'Sitio A Borrar',
            'contrasena_encriptada' => 'pass',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/logins/{$login->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('secretos.logins', [
            'id' => $login->id,
        ]);
    }

    public function test_no_permite_eliminar_login_de_otro_usuario(): void
    {
        $otroUsuario = UsuarioModelo::create([
            'nombre_completo'    => 'Otro Usuario',
            'correo_electronico' => 'otro3@example.com',
            'hash_contrasena'    => 'hash',
            'sal_contrasena'     => 'sal',
            'estado'             => 'activo',
        ]);

        $loginAjeno = LoginModelo::create([
            'usuario_id'            => $otroUsuario->id,
            'nombre_sitio'          => 'Sitio Ajeno Intacto',
            'contrasena_encriptada' => 'pass',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/logins/{$loginAjeno->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('secretos.logins', [
            'id' => $loginAjeno->id,
        ]);
    }
}
