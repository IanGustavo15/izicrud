<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordemdeservicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cliente')
                ->constrained('clientes')
                ->cascadeOnDelete();
            $table->foreignId('id_veiculo')
                ->constrained('veiculos')
                ->cascadeOnDelete();
            $table->datetime('data_de_entrada');
            $table->datetime('data_de_saida');
            $table->integer('status', false, true);
            $table->float('valor_total', 2);
            $table->text('observacao');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordemdeservicos');
    }
};
