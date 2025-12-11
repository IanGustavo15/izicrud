<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabalhadors', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->integer('especialidade', false, true);
            $table->float('valorHora', 2);
            $table->integer('status', false, true);
            $table->float('qualidade', 2);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabalhadors');
    }
};
