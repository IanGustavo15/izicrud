<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscricaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_users')
                ->constrained('users')
                ->cascadeOnDelete();
                $table->foreignId('id_simulado')
                ->constrained('simulados')
                ->cascadeOnDelete();
            $table->date('data_inscricao');
            $table->string('status');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        $faker = Faker::create('pt_BR');

        // Obter IDs válidos
        $userIds = DB::table('users')->pluck('id')->toArray();
        $simuladoIds = DB::table('simulados')->pluck('id')->toArray();

        if (empty($userIds) || empty($simuladoIds)) {
            throw new \Exception('As tabelas users ou simulados estão vazias.');
        }

        // Para cada simulado, criar entre 10 e 100 inscrições
        foreach ($simuladoIds as $simuladoId) {
            $numInscricoes = $faker->numberBetween(10, 100);
            $selectedUserIds = $faker->randomElements($userIds, $numInscricoes);
            foreach ($selectedUserIds as $userId) {
                DB::table('inscricaos')->insert([
                    'id_users' => $userId,
                    'id_simulado' => $simuladoId,
                    'data_inscricao' => $faker->dateTimeThisYear,
                    'status' => $faker->randomElement(['Pendente', 'Confirmada', 'Em Prova', 'Prova Finalizada', 'Cancelada']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricaos');
    }
};
