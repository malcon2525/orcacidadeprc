<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cotacoes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('descricao');
            $table->unsignedBigInteger('entidade_orcamentaria_id');
            $table->unsignedBigInteger('orcamento_id')->nullable();
            $table->enum('origem', ['entidade_orcamentaria', 'orcamento']); // igual ao migrate de bdis
            $table->string('unidade')->nullable();
            $table->decimal('valor_final', 15, 2)->nullable();
            $table->enum('tipo_valor_final', ['media', 'mediana', 'menor_valor', 'manual']);
            $table->timestamps();

            $table->foreign('entidade_orcamentaria_id')->references('id')->on('entidades_orcamentarias');
            $table->foreign('orcamento_id')->references('id')->on('orcamentos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotacoes');
    }
}; 