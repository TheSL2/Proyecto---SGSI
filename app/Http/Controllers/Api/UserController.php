<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('area')->orderBy('name')->get();
        return UserResource::collection($usuarios);
    }

    public function show($id)
    {
        $usuario = User::with('area')->find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return new UserResource($usuario);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $usuario = User::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        if (!in_array($request->user()->rol, ['Administrador'])) {
            return response()->json([
                'message' => 'Solo un Administrador puede modificar los informacion de un Usuario.',
            ], 403);
        }

        $usuario->update($request->validated());
        return new UserResource($usuario->load('area'));
    }
}
