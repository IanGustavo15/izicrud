<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pecas', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->string('codigo_unico')->unique();
            $table->float('preco_de_custo', 2);
            $table->float('preco_de_venda', 2);
            $table->integer('quantidade', false, true);
            $table->integer('estoque', false, true);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pecas');
    }
};
