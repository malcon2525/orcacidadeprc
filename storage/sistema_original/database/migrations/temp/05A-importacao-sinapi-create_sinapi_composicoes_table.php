<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sinapi_composicoes', function (Blueprint $table) {
            $table->id();
            $table->string('grupo');
            $table->string('codigo_composicao');
            $table->text('descricao');
            $table->string('unidade')->nullable();
            $table->decimal('custo_pr', 12, 2)->nullable();
            $table->date('data_base');
            $table->date('data_emissao')->nullable();
            $table->string('desoneracao', 3); // 'com' ou 'sem'
            $table->text('log_erro')->nullable();
            $table->timestamps();

            $table->unique(['codigo_composicao', 'data_base', 'desoneracao'], 'comp_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinapi_composicoes');
    }
};
