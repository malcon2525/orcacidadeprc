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
        Schema::create('OC01B_composicao_propria_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('composicao_propria_id')->constrained('OC01A_composicoes_proprias')->onDelete('cascade')->comment('Referência à composição principal');
            $table->enum('referencia', ['SINAPI', 'DERPR', 'PERSONALIZADA'])->comment('Fonte de referência do item');
            $table->string('codigo_item', 10)->comment('Código do item na fonte de referência');
            $table->text('descricao')->comment('Descrição detalhada do item');
            $table->string('unidade', 5)->comment('Unidade de medida do item');
            $table->date('data_base_sinapi')->nullable()->comment('Data base SINAPI utilizada (quando referencia = SINAPI)');
            $table->date('data_base_derpr')->nullable()->comment('Data base DERPR utilizada (quando referencia = DERPR)');
            $table->enum('desoneracao', ['com', 'sem'])->nullable()->comment('Tipo de desoneração utilizada na consulta (quando referencia = SINAPI/DERPR)');
            $table->decimal('valor_mat_equip', 12, 2)->comment('Valor de materiais e equipamentos');
            $table->decimal('valor_mao_obra', 12, 2)->comment('Valor de mão de obra');
            $table->decimal('valor_total', 12, 2)->comment('Valor total do item');
            $table->decimal('coeficiente', 12, 5)->comment('Coeficiente de ajuste');
            $table->decimal('valor_mat_equip_ajustado', 12, 2)->comment('Valor ajustado de materiais e equipamentos após aplicação do coeficiente');
            $table->decimal('valor_mao_obra_ajustado', 12, 2)->comment('Valor ajustado de mão de obra após aplicação do coeficiente');
            $table->decimal('valor_total_ajustado', 12, 2)->comment('Valor total ajustado do item após aplicação do coeficiente');
            $table->timestamps();
            
            // Índices para performance
            $table->index('composicao_propria_id');
            $table->index('referencia');
            $table->index('codigo_item');
            $table->index('data_base_sinapi');
            $table->index('data_base_derpr');
            $table->index('desoneracao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('OC01B_composicao_propria_items');
    }
};
