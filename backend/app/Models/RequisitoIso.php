<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitoIso extends Model
{
    use HasFactory;

    protected $fillable = [
        'categoria',
        'codigo',
        'descripcion',
        'orientacion_implementacion'
    ];
}