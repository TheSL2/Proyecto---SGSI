<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\RequisitoIso;
use App\Models\Auditoria;
use App\Models\Hallazgo;
use App\Models\Evidencia;

class ChecklistAuditoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditoria_id',
        'requisito_iso_id',
        'estado_cumplimiento',
        'observaciones',
        'justificacion'
    ];

    public function requisitoIso(): BelongsTo
    {
        return $this->belongsTo(RequisitoIso::class, 'requisito_iso_id');
    }

    public function auditoria(): BelongsTo
    {
        return $this->belongsTo(Auditoria::class, 'auditoria_id');
    }

    public function hallazgos(): HasMany
    {
        return $this->hasMany(Hallazgo::class, 'checklist_id');
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class, 'checklist_id');
    }
}