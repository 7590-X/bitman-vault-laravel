<?php

namespace App\Presentation\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Application\Comandos\ManejadorObtenerLlavesSsh;
use App\Application\Comandos\ComandoGuardarLlaveSsh;
use App\Application\Comandos\ManejadorGuardarLlaveSsh;
use App\Application\Comandos\ComandoEliminarLlaveSsh;
use App\Application\Comandos\ManejadorEliminarLlaveSsh;

class ControladorLlaveSsh extends Controller
{
    /**
     * Obtiene la lista de todas las llaves SSH del usuario autenticado.
     */
    public function index(ManejadorObtenerLlavesSsh $manejador)
    {
        $usuario = (int) auth('api')->id();
        if (!$usuario) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $llaves = $manejador->ejecutar($usuario);

        return response()->json($llaves);
    }

    /**
     * Guarda una nueva llave SSH para el usuario autenticado.
     */
    public function store(Request $request, ManejadorGuardarLlaveSsh $manejador)
    {
        $usuario = (int) auth('api')->id();
        if (!$usuario) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $validated = $request->validate([
            'nombre'                   => 'required|string|max:255',
            'llave_privada_encriptada' => 'required|string',
            'llave_publica'            => 'required|string',
            'frase_paso_encriptada'    => 'nullable|string',
        ]);

        $comando = new ComandoGuardarLlaveSsh(
            usuarioId: $usuario,
            nombre: $validated['nombre'],
            llavePrivadaEncriptada: $validated['llave_privada_encriptada'],
            llavePublica: $validated['llave_publica'],
            frasePasoEncriptada: $validated['frase_paso_encriptada'] ?? null
        );

        $llaveGenerada = $manejador->ejecutar($comando);

        return response()->json([
            'mensaje' => 'Llave SSH guardada correctamente',
            'datos' => $llaveGenerada
        ], 201);
    }

    /**
     * Elimina una llave SSH perteneciente al usuario autenticado.
     */
    public function destroy(int $id, ManejadorEliminarLlaveSsh $manejador)
    {
        $usuario = (int) auth('api')->id();
        if (!$usuario) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $comando = new ComandoEliminarLlaveSsh(
            id: (int)$id,
            usuarioId: $usuario
        );

        $eliminado = $manejador->ejecutar($comando);

        if (!$eliminado) {
            return response()->json([
                'error' => 'La llave SSH no fue encontrada o no pertenece al usuario.'
            ], 404);
        }

        return response()->json([
            'mensaje' => 'Llave SSH eliminada correctamente'
        ], 200);
    }
}
