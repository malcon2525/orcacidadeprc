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
        Schema::create('OC01A_composicoes_proprias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->nullable()->comment('Código da composição');
            $table->string('descricao', 255)->comment('Descrição da composição');
            $table->string('unidade', 10)->comment('Unidade de medida');
            $table->decimal('valor_total_mat_equip', 12, 2)->comment('Valor total de materiais e equipamentos');
            $table->decimal('valor_total_mao_obra', 12, 2)->comment('Valor total de mão de obra');
            $table->decimal('valor_total_geral', 12, 2)->comment('Valor total geral da composição');
            $table->timestamps();
            
            // Índices para performance
            $table->index('codigo');
            $table->index('descricao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('OC01A_composicoes_proprias');
    }
};
