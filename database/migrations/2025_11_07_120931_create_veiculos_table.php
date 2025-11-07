<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cliente')
                ->constrained('clientes')
                ->cascadeOnDelete();
            $table->string('placa')->unique();
            $table->string('modelo');
            $table->integer('ano', false, true);
            $table->integer('tipo', false, true);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};
