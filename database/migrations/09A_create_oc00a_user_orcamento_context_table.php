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
        Schema::create('oc00a_user_orcamento_context', function (Blueprint $table) {
            $table->id();
            
            // Usuário que possui o contexto
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Entidade orçamentária selecionada
            $table->unsignedBigInteger('entidade_orcamentaria_id');
            $table->foreign('entidade_orcamentaria_id')->references('id')->on('entidades_orcamentarias')->onDelete('cascade');
            
            // Data base SINAPI selecionada
            $table->date('data_base_sinapi');
            
            // Data base DERPR selecionada
            $table->date('data_base_derpr');
            
            // Timestamps
            $table->timestamps();
            
            // Índices para performance
            $table->index(['user_id']);
            $table->index(['entidade_orcamentaria_id']);
            $table->index(['data_base_sinapi']);
            $table->index(['data_base_derpr']);
            
            // Garantir que cada usuário tenha apenas um contexto
            $table->unique('user_id');
            
            // Comentário da tabela
            $table->comment('Armazena o contexto de trabalho orçamentário de cada usuário (entidade + datas base SINAPI/DERPR)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oc00a_user_orcamento_context');
    }
};
