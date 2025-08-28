<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sinapi_mao_de_obra', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_composicao', 20);
            $table->text('descricao');
            $table->string('unidade', 10);
            $table->decimal('percentagem_pr', 10, 6); // percentual ex: 0.271700 = 27,17%
            $table->date('data_emissao');
            $table->date('data_base');
            $table->string('desoneracao', 3); // 'com' ou 'sem'
            $table->timestamps();

            $table->unique(['codigo_composicao', 'data_base', 'desoneracao'], 'sinapi_mao_obra_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinapi_mao_de_obra');
    }
};
