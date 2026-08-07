<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hallazgos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auditoria_id')->constrained('auditorias')->onDelete('cascade');
            $table->foreignId('checklist_id')->nullable()->constrained('checklist_auditorias')->onDelete('cascade');
            $table->enum('tipo_hallazgo', ['No Conforme Mayor', 'No Conforme Menor', 'Oportunidad de Mejora', 'Observacion']);
            $table->text('descripcion');
            $table->text('evidencia_objetiva')->nullable();
            $table->enum('estado', ['Abierto', 'En Proceso', 'Cerrado'])->default('Abierto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hallazgos');
    }
};