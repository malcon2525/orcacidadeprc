<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('derpr_transportes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_servico', 10);
            $table->string('descricao_servico');
            $table->string('unidade_servico', 5);
            $table->date('data_base');
            $table->string('desoneracao', 3);
            $table->text('descricao');
            $table->string('codigo', 5);
            $table->string('unidade', 5);
            $table->string('formula1', 30);
            $table->string('formula2', 30);
            $table->decimal('custo', 12, 2);
            $table->decimal('consumo', 12, 4);
            $table->decimal('custo_unitario', 12, 2);
            $table->timestamps();

            $table->unique(['codigo_servico', 'codigo', 'data_base', 'desoneracao'], 'transportes_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('derpr_transportes');
    }
};
