<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hallazgo extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_id',
        'tipo_hallazgo',
        'clausula_o_control',
        'descripcion',
        'estado',
        'fecha_notificacion',
        'estado_notificacion',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(ChecklistAuditoria::class, 'checklist_id');
    }

    public function accionesCorrectivas(): HasMany
    {
        return $this->hasMany(AccionCorrectiva::class, 'hallazgo_id');
    }
}