<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cotacao_fornecedores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cotacao_id');
            $table->unsignedBigInteger('fornecedor_id');
            $table->decimal('valor_total', 15, 2);
            $table->decimal('mao_obra', 15, 2)->nullable();
            $table->decimal('mat_equip', 15, 2)->nullable();
            $table->date('data')->nullable();
            $table->string('arquivo')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->foreign('cotacao_id')->references('id')->on('cotacoes')->onDelete('cascade');
            $table->foreign('fornecedor_id')->references('id')->on('fornecedores');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotacao_fornecedores');
    }
}; 