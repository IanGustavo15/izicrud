<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opcaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_questao')
                ->constrained('questaos')
                ->cascadeOnDelete();
            $table->string('letra');
            $table->text('texto_opcao');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        $faker = Faker::create('pt_BR');

        $questaoIds = DB::table('questaos')->pluck('id')->toArray();

        if (empty($questaoIds)) {
            throw new \Exception('As tabela questaos esta vazia. Popule antes de inserir ranks.');
        }

        // Para cada questão, criar 5 opções (A, B, C, D, E)
        foreach ($questaoIds as $questaoId) {
            foreach (['A', 'B', 'C', 'D', 'E'] as $letra) {
                DB::table('opcaos')->insert([
                    'id_questao' => $questaoId,
                    'letra' => $letra,
                    'texto_opcao' => $faker->sentence(10),
                ]);
            }
        }

    }

    public function down(): void
    {
        Schema::dropIfExists('opcaos');
    }
};
