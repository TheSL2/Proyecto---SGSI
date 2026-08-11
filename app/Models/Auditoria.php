<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auditoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'objetivo',
        'alcance',
        'fecha_inicio',
        'fecha_fin',
        'auditor_lider_id',
        'estado',
        'conclusiones'
    ];

    public function auditorLider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_lider_id');
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'auditoria_areas');
    }
    
    public function equipoAuditor(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'auditoria_users', 'auditoria_id', 'user_id');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(ChecklistAuditoria::class, 'auditoria_id');
    }
}