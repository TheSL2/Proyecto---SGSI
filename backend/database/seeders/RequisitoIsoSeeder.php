<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequisitoIso;

class RequisitoIsoSeeder extends Seeder
{
    public function run(): void
    {
        RequisitoIso::create([
            'categoria' => 'Clausula',
            'codigo' => '5.1',
            'descripcion' => 'Liderazgo y compromiso de la dirección con el SGSI',
            'orientacion_implementacion' => 'La alta dirección debe demostrar liderazgo y compromiso con respecto al SGSI.'
        ]);
        RequisitoIso::create([
            'categoria' => 'Anexo A',
            'codigo' => 'A.5.1',
            'descripcion' => 'Políticas para la seguridad de la información',
            'orientacion_implementacion' => 'Las políticas deben ser definidas, aprobadas por la dirección y comunicadas.'
        ]);
    }
}