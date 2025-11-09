<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simuladoquestaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_simulado')
                ->constrained('simulados')
                ->cascadeOnDelete();
            $table->foreignId('id_questao')
                ->constrained('questaos')
                ->cascadeOnDelete();
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        $faker = Faker::create('pt_BR');

        // Obter IDs válidos
        $simuladoIds = DB::table('simulados')->pluck('id')->toArray();
        $questaoIds = DB::table('questaos')->pluck('id')->toArray();

        if (empty($simuladoIds) || empty($questaoIds)) {
            throw new \Exception('As tabelas simulados ou questoes estão vazias.');
        }

        // Para cada simulado, associar 60 questões aleatórias
        foreach ($simuladoIds as $simuladoId) {
            // Selecionar 60 IDs de questões aleatoriamente
            $selectedQuestaoIds = $faker->randomElements($questaoIds, 60);
            foreach ($selectedQuestaoIds as $questaoId) {
                DB::table('simuladoquestaos')->insert([
                    'id_simulado' => $simuladoId,
                    'id_questao' => $questaoId,
                ]);
            }
        }

    }

    public function down(): void
    {
        Schema::dropIfExists('simuladoquestaos');
    }
};
