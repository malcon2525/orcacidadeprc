<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('derpr_itens_incidencia', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_servico');
            $table->string('descricao_servico');
            $table->string('unidade_servico');
            $table->date('data_base');
            $table->string('desoneracao', 3);
            $table->text('descricao');
            $table->string('codigo');
            $table->decimal('percentagem', 12, 4);
            $table->string('tem_mo', 3);
            $table->decimal('custo', 12, 2);
            $table->timestamps();

            $table->unique(['codigo_servico', 'codigo', 'data_base', 'desoneracao'], 'incidencia_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('derpr_itens_incidencia');
    }
};
