<?php

/**
 * Migración para crear la tabla `logins` en el esquema `secretos`.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Crea la tabla secretos.logins con sus restricciones y llaves foráneas.
     */
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS secretos');

        DB::statement('
            CREATE TABLE IF NOT EXISTS secretos.logins (
                id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
                usuario_id BIGINT NOT NULL,
                nombre_sitio VARCHAR(150) NOT NULL,
                url TEXT NULL,
                usuario_login VARCHAR(150) NULL,
                contrasena_encriptada TEXT NOT NULL,
                notas TEXT NULL,
                creado_en TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                actualizado_en TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                CONSTRAINT fk_logins_usuarios FOREIGN KEY (usuario_id)
                    REFERENCES secretos.usuarios (id) ON DELETE CASCADE
            )
        ');
    }

    /**
     * Elimina la tabla secretos.logins.
     */
    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS secretos.logins');
    }
};
