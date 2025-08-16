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
        Schema::create('composicao_propria_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('composicao_propria_id')->constrained('composicao_proprias')->onDelete('cascade');
            $table->string('referencia', 20);
            $table->string('codigo_item', 10);
            $table->text('descricao');
            $table->string('unidade', 5);
            $table->decimal('quantidade', 12, 2);
            $table->decimal('coeficiente', 12, 5);
            $table->decimal('valor_unitario', 12, 2);
            $table->decimal('valor_mat_equip', 12, 2);
            $table->decimal('valor_mao_obra', 12, 2);
            $table->decimal('valor_total', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('composicao_propria_items');
    }
};
