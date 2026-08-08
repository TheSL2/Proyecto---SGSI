<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisito_isos', function (Blueprint $table) {
            $table->id();
            $table->string('categoria', 50);
            $table->string('codigo', 50);
            $table->text('descripcion');
            $table->boolean('aplicable')->default(true);
            $table->text('orientacion_implementacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisito_isos');
    }
};