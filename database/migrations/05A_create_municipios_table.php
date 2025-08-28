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
        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->string('prefeito', 255);
            $table->string('email', 255)->unique()->nullable();
            $table->string('endereco_prefeitura', 255);
            $table->string('codigo_ibge', 20)->unique();
            $table->integer('populacao');
            $table->string('cep', 10);
            $table->string('telefone', 20);
            $table->string('cnpj', 20)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
