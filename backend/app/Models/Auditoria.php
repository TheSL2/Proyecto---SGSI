<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    public function auditorLider()
    {
        return $this->belongsTo(User::class, 'auditor_lider_id');
    }

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'auditoria_areas');
    }
}