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
        Schema::create('user_entidades_orcamentarias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('entidade_orcamentaria_id')->constrained('entidades_orcamentarias')->onDelete('cascade');
            $table->boolean('ativo')->default(true);
            $table->timestamp('data_vinculacao')->useCurrent();
            $table->foreignId('vinculado_por_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Constraint para evitar vínculos duplicados
            $table->unique(['user_id', 'entidade_orcamentaria_id'], 'unique_user_entidade');
            
            // Índices para performance (com nomes customizados)
            $table->index(['user_id', 'ativo'], 'idx_user_ativo');
            $table->index(['entidade_orcamentaria_id', 'ativo'], 'idx_entidade_ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_entidades_orcamentarias');
    }
};
