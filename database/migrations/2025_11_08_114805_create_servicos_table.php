<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('descricao');
            $table->float('preco_mao_de_obra', 2);
            $table->integer('tempo_estimado', false, true);
            $table->integer('quantidade_peca', false, true);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        DB::table('servicos')->insert([
            'nome' => 'Troca de óleo',
            'descricao' => 'Óleo + filtro',
            'preco_mao_de_obra' => '10',
            'tempo_estimado' => '30',
            'quantidade_peca' => '1',
            'deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('servicos')->insert([
            'nome' => 'Troca de pneu',
            'descricao' => 'Pneu + algo',
            'preco_mao_de_obra' => '20',
            'tempo_estimado' => '60',
            'quantidade_peca' => '1',
            'deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};
