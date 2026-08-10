<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AuditoriaService
{
    public static function log(string $accion, array $datos = []): void
    {
        Log::channel('audit')->info($accion, array_merge([
            'usuario_id' => auth()->id() ?? 'anonimo',
            'email' => auth()->user()?->email ?? 'anonimo',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ], $datos));
    }
}