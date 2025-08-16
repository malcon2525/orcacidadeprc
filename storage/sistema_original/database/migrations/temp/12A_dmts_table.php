<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dmts', function (Blueprint $table) {
            $table->id();

            $table->string('codigo_material', 10);
            $table->string('nome_material', 255);
            $table->string('origem', 150)->nullable();
            $table->string('destino', 150)->nullable();
            $table->string('sigla_transporte', 3);
            $table->enum('tipo', ['local', 'comercial']);
            $table->decimal('x1', 10, 2)->default(0);
            $table->decimal('x2', 10, 2)->default(0);

            $table->unsignedBigInteger('id_municipio')->nullable();
            $table->unsignedBigInteger('id_entidade_orcamentaria');
            $table->timestamps();

            $table->foreign('id_municipio')->references('id')->on('municipios')->nullOnDelete();
            $table->foreign('id_entidade_orcamentaria')->references('id')->on('entidades_orcamentarias')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dmts');
    }
}; 


