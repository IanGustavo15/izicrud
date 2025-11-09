<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_inscricao')
                ->constrained('inscricaos')
                ->cascadeOnDelete();
            $table->integer('pontuacao_total');
            $table->integer('acertos');
            $table->integer('erros');
            $table->integer('tempo_total_minutos');
            $table->decimal('percentual_acerto');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        $faker = Faker::create('pt_BR');

        $inscricaoIds = DB::table('inscricaos')->pluck('id')->toArray();

        if (empty($inscricaoIds)) {
            throw new \Exception('A tabela inscricaos está vazia.');
        }

        foreach ($inscricaoIds as $inscricaoId) {
            $respostas = DB::table('respostas')->where('id_inscricao', $inscricaoId)->get();
            $acertos = $respostas->where('correta', true)->count();
            $erros = $respostas->where('correta', false)->count();
            $totalQuestoes = $acertos + $erros;
            $pontuacao_total = $acertos * 10; // Exemplo: 10 pontos por acerto
            $percentual_acerto = $totalQuestoes > 0 ? ($acertos / $totalQuestoes) * 100 : 0;

            DB::table('resultados')->insert([
                'id_inscricao' => $inscricaoId,
                'pontuacao_total' => $pontuacao_total,
                'acertos' => $acertos,
                'erros' => $erros,
                'tempo_total_minutos' => $faker->numberBetween(30, 180),
                'percentual_acerto' => round($percentual_acerto, 2),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }

    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
};
