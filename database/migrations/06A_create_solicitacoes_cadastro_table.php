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
            
            // === DADOS DO USUÁRIO (criado temporariamente) ===
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // === ENTIDADE ORÇAMENTÁRIA SOLICITADA ===
            $table->foreignId('entidade_orcamentaria_id')->constrained('entidades_orcamentarias')->onDelete('cascade');
            
            // === DADOS PESSOAIS DO VISITANTE (para aprovação) ===
            $table->string('visitante_nome', 255); // Nome completo
            $table->string('visitante_email', 255); // Email
            $table->string('visitante_telefone', 20)->nullable(); // Telefone (opcional)
            $table->string('visitante_cpf', 14)->nullable(); // CPF (opcional)
            $table->string('visitante_cargo', 255)->nullable(); // Cargo/Função (opcional)
            
            // === LOCALIZAÇÃO DO VISITANTE ===
            $table->string('visitante_municipio', 255); // Município onde mora/trabalha
            $table->string('visitante_uf', 2); // UF onde mora/trabalha
            
            // === DADOS DA SOLICITAÇÃO ===
            $table->text('justificativa'); // Por que precisa do acesso
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            
            // === DADOS DA APROVAÇÃO ===
            $table->text('observacoes_aprovacao')->nullable(); // Comentários do aprovador
            $table->timestamp('data_solicitacao')->useCurrent(); // Quando foi solicitado
            $table->timestamp('data_aprovacao')->nullable(); // Quando foi aprovado/rejeitado
            $table->foreignId('aprovado_por_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // === ÍNDICES PARA PERFORMANCE E FILTROS ===
            
            // Índices principais para listagem e filtros
            $table->index(['status', 'data_solicitacao'], 'idx_status_data');
            $table->index(['entidade_orcamentaria_id', 'status'], 'idx_entidade_status');
            $table->index(['visitante_nome'], 'idx_visitante_nome');
            $table->index(['visitante_email'], 'idx_visitante_email');
            $table->index(['visitante_uf'], 'idx_visitante_uf');
            $table->index(['user_id'], 'idx_user_id');
            $table->index(['data_solicitacao'], 'idx_data_solicitacao');
            
            // Índice composto para filtro complexo na tela de aprovação
            $table->index(['status', 'visitante_uf', 'data_solicitacao'], 'idx_aprovacao_filtros');
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
