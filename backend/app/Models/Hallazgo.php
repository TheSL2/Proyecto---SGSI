<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auditoria;
use App\Models\ChecklistAuditoria;
use App\Models\AccionCorrectiva;

class Hallazgo extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditoria_id',
        'checklist_id',
        'tipo_hallazgo',
        'descripcion',
        'evidencia_objetiva',
        'estado',
    ];

    public function auditoria()
    {
        return $this->belongsTo(Auditoria::class, 'auditoria_id');
    }

    public function checklist()
    {
        return $this->belongsTo(ChecklistAuditoria::class, 'checklist_id');
    }

    public function accionesCorrectivas()
    {
        return $this->hasMany(AccionCorrectiva::class, 'hallazgo_id');
    }
}