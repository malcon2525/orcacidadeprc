<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('derpr_formula_transportes', function (Blueprint $table) {
            $table->id();
            $table->date('data_base');
            $table->enum('desoneracao', ['com', 'sem']);
            $table->string('codigo', 10);
            $table->string('descricao', 255);
            $table->string('unidade', 5);
            $table->string('formula_transporte', 30);
            $table->timestamps();

            // Índices para melhor performance
            $table->index(['data_base', 'desoneracao']);
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('derpr_formula_transportes');
    }
};
