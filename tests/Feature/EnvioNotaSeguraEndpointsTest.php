<?php

namespace Tests\Feature;

use App\Infrastructure\Modelos\EnvioNotaSeguraModelo;
use App\Infrastructure\Modelos\UsuarioModelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class EnvioNotaSeguraEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private UsuarioModelo $usuario;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuario = UsuarioModelo::create([
            'nombre_completo'    => 'Emisor Nota',
            'correo_electronico' => 'emisor@example.com',
            'hash_contrasena'    => 'hash',
            'sal_contrasena'     => 'sal',
            'estado'             => 'activo',
        ]);

        $this->token = JWTAuth::fromUser($this->usuario);
    }

    public function test_crear_y_aperturar_nota_segura_exitosamente(): void
    {
        $payload = [
            'titulo'                          => 'Mensaje Secreto',
            'correo_destino'                  => 'destino@example.com',
            'texto_nota'                      => 'Este es el contenido ultra secreto.',
            'codigo_apertura'                 => 'CLAVE123',
            'duracion_visualizacion_segundos' => 45,
            'minutos_expiracion'              => 60,
        ];

        $crearResponse = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/enviar-notas', $payload);

        $crearResponse->assertStatus(201)
            ->assertJsonPath('mensaje', 'Nota segura registrada y lista para enviar.');

        $tokenAcceso = $crearResponse->json('payload.token_acceso');
        $this->assertNotEmpty($tokenAcceso);

        // Aperturar la nota por primera vez con el código correcto (soporta mayúsculas y minúsculas)
        $aperturarResponse = $this->postJson("/api/enviar-notas/aperturar/{$tokenAcceso}", [
            'codigo_apertura' => 'clave123',
        ]);

        $aperturarResponse->assertStatus(200)
            ->assertJsonPath('payload.titulo', 'Mensaje Secreto')
            ->assertJsonPath('payload.contenido', 'Este es el contenido ultra secreto.')
            ->assertJsonPath('payload.duracion_visualizacion_segundos', 45);

        // Verificar que en base de datos ya fue destruido el contenido y marcada como aperturada
        $notaEnBd = EnvioNotaSeguraModelo::where('token_acceso', $tokenAcceso)->first();
        $this->assertEquals('aperturada', $notaEnBd->estado);
        $this->assertNull($notaEnBd->contenido_encriptado);

        // Intentar aperturar por segunda vez debe retornar 410 Gone (autodestruida)
        $reaperturarResponse = $this->postJson("/api/enviar-notas/aperturar/{$tokenAcceso}", [
            'codigo_apertura' => 'clave123',
        ]);

        $reaperturarResponse->assertStatus(410)
            ->assertJsonPath('mensaje', 'Esta nota ya fue aperturada previamente y su contenido ha sido eliminado.');
    }

    public function test_aperturar_nota_con_codigo_incorrecto_falla(): void
    {
        $payload = [
            'titulo'                          => 'Prueba Código Erróneo',
            'correo_destino'                  => 'destino2@example.com',
            'texto_nota'                      => 'Texto secreto',
            'codigo_apertura'                 => 'CLAVE_CORRECTA',
            'duracion_visualizacion_segundos' => 30,
            'minutos_expiracion'              => 30,
        ];

        $crearResponse = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/enviar-notas', $payload);

        $tokenAcceso = $crearResponse->json('payload.token_acceso');

        $aperturarResponse = $this->postJson("/api/enviar-notas/aperturar/{$tokenAcceso}", [
            'codigo_apertura' => 'CODIGO_INCORRECTO',
        ]);

        $aperturarResponse->assertStatus(400)
            ->assertJsonPath('mensaje', 'El código de apertura ingresado es incorrecto.');
    }
}
