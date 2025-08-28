<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('derpr_composicoes', function (Blueprint $table) {
            $table->id();
            $table->string('grupo', 100);
            $table->date('data_base');
            $table->string('desoneracao', 3); // 'com' ou 'sem'
            $table->string('codigo', 10);
            $table->string('descricao');
            $table->string('unidade', 5);
            $table->decimal('custo_execucao', 12, 2);
            $table->decimal('custo_material', 12, 2);
            $table->decimal('custo_sub_servico', 12, 2);
            $table->decimal('custo_unitario', 12, 2);
            $table->string('transporte', 20);
            $table->timestamps();

            // Garante unicidade lógica
            $table->unique(['codigo', 'data_base', 'desoneracao'], 'comp_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('derpr_composicoes');
    }
};
