<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_simulado')
                ->constrained('simulados')
                ->cascadeOnDelete();
            $table->foreignId('id_users')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->integer('pontuacao_final');
            $table->integer('posicao_rank');
            $table->boolean('classificacao');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        $faker = Faker::create('pt_BR');

        $simuladoIds = DB::table('simulados')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        if (empty($simuladoIds) || empty($userIds)) {
            throw new \Exception('As tabelas simulados ou users estão vazias. Popule-as antes de inserir ranks.');
        }

        for ($i = 0; $i < 10; $i++) {
            DB::table('ranks')->insert([
                'id_simulado' => $faker->randomElement($simuladoIds),
                'id_users' => $faker->randomElement($userIds),
                'pontuacao_final' => $faker->numberBetween(0, 100),
                'posicao_rank' => $faker->numberBetween(1, 50),
                'classificacao' => $faker->numberBetween(1, 5),
            ]);
        }


    }

    public function down(): void
    {
        Schema::dropIfExists('ranks');
    }
};
