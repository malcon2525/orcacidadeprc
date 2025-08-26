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
            $table->string('razao_social', 255)->unique();
            $table->string('nome_fantasia', 255)->unique();
            $table->enum('tipo_organizacao', ['municipio', 'secretaria', 'órgão', 'autarquia', 'outros']);
            $table->string('email', 255)->unique()->nullable();
            $table->string('endereco', 255)->nullable();
            
            // Campos de Jurisdição/Abrangência
            $table->enum('nivel_administrativo', ['municipal', 'estadual', 'federal'])->default('municipal');
            $table->string('jurisdicao_nome', 255); // "Curitiba", "Paraná", "Brasil"
            $table->string('jurisdicao_codigo_ibge', 20)->nullable(); // só preenchido para municipal
            
            $table->integer('populacao')->nullable();
            $table->string('cep', 10)->nullable();
            $table->string('telefone', 20);
            $table->string('cnpj', 20)->unique()->nullable();
            $table->string('responsavel', 255);
            $table->string('responsavel_cargo', 100);
            $table->string('responsavel_telefone', 20)->nullable();
            $table->string('responsavel_email', 100)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            // Índices para performance
            $table->index(['nivel_administrativo', 'jurisdicao_codigo_ibge'], 'idx_nivel_jurisdicao');
            $table->index(['ativo', 'nivel_administrativo'], 'idx_ativo_nivel');
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