<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questaos', function (Blueprint $table) {
            $table->id();
            $table->text('texto_questao');
            $table->string('area_concurso');
            $table->string('diciplina');
            $table->string('nivel_dificuldade');
            $table->string('gabarito_correto');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        $faker = Faker::create('pt_BR');

        for ($i = 0; $i < 1000; $i++) {
            DB::table('questaos')->insert([
                'texto_questao' => $faker->text(200),
                'area_concurso' => $faker->randomElement(['saude', 'educacao', 'seguranca']),
                'diciplina' => $faker->randomElement(['matematica', 'portugues', 'historia']),
                'nivel_dificuldade' => $faker->randomElement(['fundamental', 'medio', 'superior', 'tecnico']),
                'gabarito_correto' => $faker->randomElement(['A', 'B', 'C', 'D', 'E']),
            ]);
        }

    }


    public function down(): void
    {
        Schema::dropIfExists('questaos');
    }
};
