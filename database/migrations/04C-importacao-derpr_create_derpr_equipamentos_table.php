<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('derpr_equipamentos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_servico', 10);
            $table->string('descricao_servico');
            $table->string('unidade_servico', 5);
            $table->date('data_base');
            $table->string('desoneracao', 3);
            $table->string('descricao');
            $table->string('codigo_equipamento', 10);
            $table->decimal('quantidade', 12, 4);
            $table->decimal('ut_produtiva', 12, 4);
            $table->decimal('ut_improdutiva', 12, 4);
            $table->decimal('vl_hr_prod', 12, 2);
            $table->decimal('vl_hr_imp', 12, 2);
            $table->decimal('custo_horario', 12, 2);
            $table->timestamps();

            $table->unique(['codigo_servico', 'codigo_equipamento', 'data_base', 'desoneracao'], 'equip_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('derpr_equipamentos');
    }
};
