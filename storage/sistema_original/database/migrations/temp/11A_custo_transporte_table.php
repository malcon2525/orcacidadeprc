<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custo_transporte', function (Blueprint $table) {
            $table->id();
            $table->string('sigla', 3);
            $table->string('codigo', 10);
            $table->unique('codigo');
            $table->string('descricao', 255);
            $table->string('unidade', 5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custo_transporte');
    }
}; 