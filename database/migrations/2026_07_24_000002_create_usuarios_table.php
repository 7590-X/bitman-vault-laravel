<?php

/**
 * Migración para crear la tabla `usuarios` en el esquema `secretos`.
 * Sigue la especificación del archivo data-base.md § 2.1.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla secretos.usuarios con todos sus campos y restricciones.
     */
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS secretos');

        DB::statement('
            CREATE TABLE IF NOT EXISTS secretos.usuarios (
                id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
                nombre_completo VARCHAR(150) NOT NULL,
                correo_electronico CITEXT NOT NULL,
                hash_contrasena TEXT NOT NULL,
                sal_contrasena TEXT NOT NULL,
                estado VARCHAR(20) NOT NULL DEFAULT \'activo\'
                    CHECK (estado IN (\'activo\', \'suspendido\', \'eliminado\')),
                creado_en TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                actualizado_en TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                CONSTRAINT uq_usuarios_correo_electronico UNIQUE (correo_electronico)
            )
        ');
    }

    /**
     * Elimina la tabla secretos.usuarios.
     */
    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS secretos.usuarios');
    }
};
