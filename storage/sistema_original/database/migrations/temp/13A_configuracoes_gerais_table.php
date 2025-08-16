<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('configuracoes_gerais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('entidade_orcamentaria_id');
            $table->date('data_base_derpr');
            $table->enum('derpr_desoneracao', ['com', 'sem']);
            $table->date('data_base_sinapi');
            $table->enum('sinapi_desoneracao', ['com', 'sem']);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('entidade_orcamentaria_id')->references('id')->on('entidades_orcamentarias')->restrictOnDelete();
            $table->unique('user_id'); // Um registro por usuário
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracoes_gerais');
    }
}; 