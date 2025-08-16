<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bdis', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->nullable();
            $table->enum('origem', ['entidade_orcamentaria', 'orcamento']);

            $table->foreignId('orcamento_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('entidade_orcamentaria_id')->nullable()->constrained('entidades_orcamentarias')->nullOnDelete();

            $table->boolean('analisado')->default(false);
            
            // Serviços
            $table->decimal('adm_central_servico', 5, 2)->default(0.00);
            $table->decimal('riscos_servico', 5, 2)->default(0.00);
            $table->decimal('seguros_garantia_servico', 5, 2)->default(0.00);
            $table->decimal('desp_financeira_servico', 5, 2)->default(0.00);
            $table->decimal('lucro_servico', 5, 2)->default(0.00);

            // Materiais
            $table->decimal('adm_central_material', 5, 2)->default(0.00);
            $table->decimal('riscos_material', 5, 2)->default(0.00);
            $table->decimal('seguros_garantia_material', 5, 2)->default(0.00);
            $table->decimal('desp_financeira_material', 5, 2)->default(0.00);
            $table->decimal('lucro_material', 5, 2)->default(0.00);

            // Equipamentos
            $table->decimal('adm_central_equipamento', 5, 2)->default(0.00);
            $table->decimal('riscos_equipamento', 5, 2)->default(0.00);
            $table->decimal('seguros_garantia_equipamento', 5, 2)->default(0.00);
            $table->decimal('desp_financeira_equipamento', 5, 2)->default(0.00);
            $table->decimal('lucro_equipamento', 5, 2)->default(0.00);

            // Impostos
            $table->decimal('iss_municipio', 5, 2)->default(0.00);
            $table->decimal('base_calculo_mao_obra', 5, 2)->default(0.00);
            $table->decimal('iss_calculado', 5, 2)->default(0.00);
            $table->decimal('pis', 5, 2)->default(0.00);
            $table->decimal('cofins', 5, 2)->default(0.00);
            $table->decimal('cprb', 5, 2)->default(0.00);
            $table->decimal('impostos_total', 5, 2)->default(0.00);

            // Resultado final
            $table->decimal('bdi_servico', 5, 2)->default(0.00);
            $table->decimal('bdi_material', 5, 2)->default(0.00);
            $table->decimal('bdi_equipamento', 5, 2)->default(0.00);



            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bdis');
    }
}; 