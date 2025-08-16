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
        Schema::create('composicao_proprias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->nullable();
            $table->string('descricao', 255);
            $table->string('unidade', 10);
            $table->decimal('valor_total_mat_equip', 12, 2);
            $table->decimal('valor_total_mao_obra', 12, 2);
            $table->decimal('valor_total_geral', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('composicao_proprias');
    }
};
