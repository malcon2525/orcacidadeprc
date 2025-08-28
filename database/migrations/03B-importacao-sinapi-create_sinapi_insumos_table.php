<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sinapi_insumos', function (Blueprint $table) {
            $table->id();
            $table->string('classificacao');
            $table->string('codigo_insumo');
            $table->text('descricao');
            $table->string('unidade')->nullable();
            $table->decimal('custo_pr', 12, 2)->nullable();
            $table->date('data_base');
            $table->date('data_emissao')->nullable();
            $table->string('desoneracao', 3); // 'com' ou 'sem'
            $table->timestamps();

            $table->unique(['codigo_insumo', 'data_base', 'desoneracao'], 'insumo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinapi_insumos');
    }
};
