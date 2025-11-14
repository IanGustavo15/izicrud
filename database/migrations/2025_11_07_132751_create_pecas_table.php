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

        DB::table('pecas')->insert([
            'descricao' => 'Óleo barato',
            'codigo_unico' => '0000001',
            'preco_de_custo' => '10',
            'preco_de_venda' => '15',
            'quantidade' => '50',
            'estoque' => '100',
            'deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pecas')->insert([
            'descricao' => 'Óleo Premium',
            'codigo_unico' => '0000002',
            'preco_de_custo' => '20',
            'preco_de_venda' => '25',
            'quantidade' => '25',
            'estoque' => '50',
            'deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pecas')->insert([
            'descricao' => 'Par de Retrovisor - Moto',
            'codigo_unico' => '0000003',
            'preco_de_custo' => '20',
            'preco_de_venda' => '25',
            'quantidade' => '10',
            'estoque' => '20',
            'deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pecas')->insert([
            'descricao' => 'Par de Pneu - Moto',
            'codigo_unico' => '0000004',
            'preco_de_custo' => '50',
            'preco_de_venda' => '80',
            'quantidade' => '15',
            'estoque' => '30',
            'deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pecas');
    }
};
