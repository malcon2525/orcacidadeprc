<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('derpr_mao_de_obra', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_servico', 10);
            $table->string('descricao_servico');
            $table->string('unidade_servico', 5);
            $table->date('data_base');
            $table->string('desoneracao', 3);
            $table->string('descricao');
            $table->string('codigo', 10);
            $table->decimal('eq_salarial', 12, 2);
            $table->decimal('encargos_percentagem', 12, 2);
            $table->decimal('sal_hora', 12, 2);
            $table->decimal('consumo', 12, 4);
            $table->decimal('custo_horario', 12, 2);
            $table->timestamps();

            $table->unique(['codigo_servico', 'codigo', 'data_base', 'desoneracao'], 'mao_obra_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('derpr_mao_de_obra');
    }
};
