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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nome da permissão (ex: exibir-orcamento) - ÚNICO
            $table->string('display_name'); // Nome de exibição (ex: Exibir Orçamentos)
            $table->text('description')->nullable(); // Descrição da permissão
            $table->boolean('is_active')->default(true); // Status ativo/inativo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
