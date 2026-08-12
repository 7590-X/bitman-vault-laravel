<?php

/**
 * Migración para la tabla de envío de notas seguras (secretos.envio_notas_seguras).
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla secretos.envio_notas_seguras.
     */
    public function up(): void
    {
        if (Schema::hasTable('secretos.envio_notas_seguras')) {
            return;
        }

        DB::statement('CREATE SCHEMA IF NOT EXISTS secretos');

        DB::statement("
            CREATE TABLE IF NOT EXISTS secretos.envio_notas_seguras (
                id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
                usuario_id BIGINT NOT NULL,
                titulo VARCHAR(150) NOT NULL,
                correo_destino CITEXT NOT NULL,
                contenido_encriptado TEXT NULL,
                codigo_apertura VARCHAR(50) NOT NULL,
                token_acceso UUID NOT NULL DEFAULT gen_random_uuid(),
                estado VARCHAR(30) NOT NULL DEFAULT 'no_aperturada',
                duracion_visualizacion_segundos INT NOT NULL DEFAULT 30,
                expira_en TIMESTAMPTZ NOT NULL,
                aperturado_en TIMESTAMPTZ NULL,
                creado_en TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                actualizado_en TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                CONSTRAINT fk_envio_notas_usuarios FOREIGN KEY (usuario_id)
                    REFERENCES secretos.usuarios (id) ON DELETE CASCADE,
                CONSTRAINT uq_envio_notas_token UNIQUE (token_acceso)
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_envio_notas_usuario_id ON secretos.envio_notas_seguras (usuario_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_envio_notas_token_acceso ON secretos.envio_notas_seguras (token_acceso)");
    }

    /**
     * Elimina la tabla secretos.envio_notas_seguras.
     */
    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS secretos.envio_notas_seguras CASCADE");
    }
};
