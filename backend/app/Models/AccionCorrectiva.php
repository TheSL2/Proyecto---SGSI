<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccionCorrectiva extends Model
{
    use HasFactory;

    protected $table = 'acciones_correctivas';

    protected $fillable = [
        'hallazgo_id',
        'causa_raiz',
        'descripcion_accion',
        'responsable_id',
        'fecha_limite',
        'estado',
        'evidencia_cierre_id',
        'verificado_por',
    ];

    protected $casts = [
        'fecha_limite' => 'date',
    ];


    public function verificador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }

    public function hallazgo(): BelongsTo
    {
        return $this->belongsTo(Hallazgo::class, 'hallazgo_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function evidenciaCierre(): BelongsTo
    {
        return $this->belongsTo(Evidencia::class, 'evidencia_cierre_id');
    }
}