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
        Schema::create('entidades_orcamentarias', function (Blueprint $table) {
            $table->id();
            
            // Campos ENUM (que serão SELECT no frontend)
            $table->enum('tipo_organizacao', [
                'Unidade Federativa', 
                'Secretaria', 
                'Órgão', 
                'Autarquia', 
                'Consórcio', 
                'S/A', 
                'PJ', 
                'PF'
            ]);
            
            $table->enum('nivel_administrativo', ['municipal', 'estadual', 'federal']);
            
            // Campos de Jurisdição (OBRIGATÓRIOS)
            $table->string('jurisdicao_razao_social', 255);
            $table->string('jurisdicao_nome_fantasia', 255);
            $table->string('jurisdicao_uf', 2);
            $table->string('jurisdicao_codigo_ibge', 20)->nullable();
            
            // Campos de Endereço
            $table->string('cep', 20)->nullable();
            $table->string('endereco', 255)->nullable();
            $table->string('telefone', 20)->nullable();
            
            // Email OBRIGATÓRIO
            $table->string('email', 255);
            
            // CNPJ
            $table->string('cnpj', 20)->nullable();
            
            // Status e Observação
            $table->boolean('ativo')->default(true);
            $table->text('observacao')->nullable();
            
            // Responsável (OPCIONAL)
            $table->string('responsavel', 255)->nullable();
            $table->string('responsavel_cargo', 255)->nullable();
            
            $table->timestamps();
            
            // Índices para performance
            $table->index(['tipo_organizacao', 'nivel_administrativo'], 'idx_tipo_nivel');
            $table->index(['ativo', 'jurisdicao_uf'], 'idx_ativo_uf');
            $table->index(['jurisdicao_codigo_ibge'], 'idx_codigo_ibge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entidades_orcamentarias');
    }
};