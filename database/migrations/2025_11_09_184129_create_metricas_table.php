<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metricas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_simulado')
                ->constrained('simulados')
                ->cascadeOnDelete();
            $table->decimal('media_geral_pontuacao');
            $table->integer('base_vagas');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        $faker = Faker::create('pt_BR');

        $simuladoIds = DB::table('simulados')->pluck('id')->toArray();

        if (empty($simuladoIds)) {
            throw new \Exception('A tabela simulados está vazia.');
        }

        foreach ($simuladoIds as $simuladoId) {
            DB::table('metricas')->insert([
                'id_simulado' => $simuladoId,
                'media_geral_pontuacao' => $faker->numberBetween(50, 90),
                'base_vagas' => $faker->numberBetween(10, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }

    public function down(): void
    {
        Schema::dropIfExists('metricas');
    }
};
