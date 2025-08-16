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
            $table->string('codigo_ibge', 20)->unique()->nullable();
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