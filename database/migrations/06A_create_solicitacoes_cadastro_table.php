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
        Schema::create('solicitacoes_cadastro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('municipio_id')->constrained('municipios')->onDelete('cascade');
            $table->foreignId('entidade_orcamentaria_id')->constrained('entidades_orcamentarias')->onDelete('cascade');
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            $table->text('justificativa');
            $table->text('observacoes_aprovacao')->nullable();
            $table->timestamp('data_solicitacao')->useCurrent();
            $table->timestamp('data_aprovacao')->nullable();
            $table->foreignId('aprovado_por_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Índices para performance
            $table->index(['status', 'data_solicitacao']);
            $table->index(['user_id', 'entidade_orcamentaria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_cadastro');
    }
};
