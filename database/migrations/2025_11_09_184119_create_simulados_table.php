<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulados', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao');
            $table->datetime('data_inicio');
            $table->datetime('data_fim');
            $table->integer('duracao_minutos');
            $table->integer('numero_vagas');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        $faker = Faker::create('pt_BR');

        for ($i = 0; $i < 12; $i++) {
            DB::table('simulados')->insert([
                'titulo' => $faker->sentence(5),
                'descricao' => $faker->text(200),
                'data_inicio' => $faker->dateTimeThisYear->format('Y-m-d'),
                'data_fim' => $faker->dateTimeThisYear->format('Y-m-d'),
                'duracao_minutos' => $faker->numberBetween(60, 320),
                'numero_vagas' => $faker->numberBetween(1, 150),
            ]);
        }

    }

    public function down(): void
    {
        Schema::dropIfExists('simulados');
    }
};
