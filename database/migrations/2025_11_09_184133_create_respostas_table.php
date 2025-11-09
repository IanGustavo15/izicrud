<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;




return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_inscricao')
                ->constrained('inscricaos')
                ->cascadeOnDelete();
            $table->foreignId('id_questao')
                ->constrained('questaos')
                ->cascadeOnDelete();
            $table->string('resposta_selecionada');
            $table->integer('tempo_resposta_segundos');
            $table->boolean('correta');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });


        $faker = Faker::create('pt_BR');

        $inscricaoIds = DB::table('inscricaos')->pluck('id')->toArray();
        $simuladoQuestao = DB::table('simuladoquestaos')->get()->groupBy('id_simulado');

        if (empty($inscricaoIds) || empty($simuladoQuestao)) {
            throw new \Exception('As tabelas inscricaos ou simuladoquestaos estão vazias.');
        }

        // Para cada inscrição, criar respostas para as 60 questões do simulado
        foreach ($inscricaoIds as $inscricaoId) {
            $inscricao = DB::table('inscricaos')->where('id', $inscricaoId)->first();
            $questaoIds = $simuladoQuestao[$inscricao->id_simulado]->pluck('id_questao')->toArray();

            foreach ($questaoIds as $questaoId) {
                $gabarito = DB::table('questaos')->where('id', $questaoId)->value('gabarito_correto');
                $resposta = $faker->randomElement(['A', 'B', 'C', 'D', 'E']);
                DB::table('respostas')->insert([
                    'id_inscricao' => $inscricaoId,
                    'id_questao' => $questaoId,
                    'resposta_selecionada' => $resposta,
                    'tempo_resposta_segundos' => $faker->numberBetween(10, 300),
                    'correta' => $resposta === $gabarito,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

    }

    public function down(): void
    {
        Schema::dropIfExists('respostas');
    }
};
