<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf', 14)->unique();
            $table->string('telefone', 20);
            $table->string('email');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        DB::table('clientes')->insert([
            'nome' => 'Jair Messias',
            'cpf' => '17171717171',
            'telefone' => '22222222222',
            'email' => 'bonoro@bonoro.com',
            'deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('clientes')->insert([
            'nome' => 'Luiz Inácio',
            'cpf' => '13131313131',
            'telefone' => '33333333333',
            'email' => 'lule@lule.com',
            'deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
