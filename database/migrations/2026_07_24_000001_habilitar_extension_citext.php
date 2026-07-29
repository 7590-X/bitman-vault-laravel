<?php

/**
 * Migración para habilitar la extensión CITEXT en PostgreSQL.
 * Debe ejecutarse antes de la migración de la tabla de usuarios.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Habilita la extensión CITEXT para comparaciones insensibles a mayúsculas.
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS citext');
    }

    /**
     * Deshabilita la extensión CITEXT si existe.
     */
    public function down(): void
    {
        DB::statement('DROP EXTENSION IF EXISTS citext');
    }
};
