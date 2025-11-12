<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pecaservicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_servico')
                ->constrained('servicos')
                ->cascadeOnDelete();
            $table->foreignId('id_peca')
                ->constrained('pecas')
                ->cascadeOnDelete();
            $table->integer('quantidade_peca', false, true);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pecaservicos');
    }
};
