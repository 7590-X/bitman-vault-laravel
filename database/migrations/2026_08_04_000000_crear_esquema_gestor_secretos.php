<?php

/**
 * Migración principal para crear el esquema 'secretos' y todas sus tablas, tipos,
 * funciones, procedimientos y particiones a partir del script SQL gestor_secretos_postgresql17.sql.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        // Si el esquema o la tabla 'secretos.usuarios' ya existe en la base de datos local,
        // omitir la ejecución para registrar la migración en la tabla 'migrations' sin errores.
        if (\Illuminate\Support\Facades\Schema::hasTable('secretos.usuarios')) {
            return;
        }

        $sqlPath = base_path('sql/gestor_secretos_postgresql17.sql');

        if (!file_exists($sqlPath)) {
            throw new RuntimeException("El archivo SQL de esquema no fue encontrado en: {$sqlPath}");
        }

        $sql = file_get_contents($sqlPath);
        DB::unprepared($sql);
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        DB::unprepared('DROP SCHEMA IF EXISTS secretos CASCADE;');
    }
};
