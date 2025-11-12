<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicoordemdeservicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_ordemdeservico')
                ->constrained('ordemdeservicos')
                ->cascadeOnDelete();
            $table->foreignId('id_servico')
                ->constrained('servicos')
                ->cascadeOnDelete();
            $table->integer('quantidade', false, true);
            $table->float('preco_unitario', 2);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicoordemdeservicos');
    }
};
