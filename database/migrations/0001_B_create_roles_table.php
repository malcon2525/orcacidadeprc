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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nome interno (ex: admin, gerente)
            $table->string('display_name'); // Nome de exibição (ex: Administrador, Gerente)
            $table->text('description')->nullable(); // Descrição do papel
            $table->boolean('is_active')->default(true); // Status ativo/inativo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
