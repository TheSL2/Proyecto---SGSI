<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hallazgo extends Model
{
    use HasFactory;

    protected $table = 'hallazgos';

    protected $fillable = [
        'checklist_id',
        'tipo_hallazgo',
        'descripcion',
        'fecha_notificacion',
        'estado_notificacion',
        'estado',
    ];

    protected $casts = [
        'fecha_notificacion' => 'datetime',
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