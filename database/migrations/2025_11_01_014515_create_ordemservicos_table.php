<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordemservicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_servico')
                ->constrained('servicos')
                ->cascadeOnDelete();
            $table->foreignId('id_moto')
                ->constrained('motos')
                ->cascadeOnDelete();
            $table->date('data_servico');
            $table->boolean('realizado');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordemservicos');
    }
};