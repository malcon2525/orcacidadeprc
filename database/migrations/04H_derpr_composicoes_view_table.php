<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('derpr_composicoes_view', function (Blueprint $table) {
            $table->id();
            $table->date('data_base');
            $table->string('desoneracao', 10);
            $table->string('codigo', 10);
            $table->string('descricao');
            $table->string('unidade', 10);
            $table->decimal('valor_mao_obra', 12, 2);
            $table->decimal('valor_mat_equip', 12, 2);
            $table->decimal('valor_total', 12, 2);
            $table->timestamps();
            $table->string('transporte', 20);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('derpr_composicoes_view');
    }
}; 