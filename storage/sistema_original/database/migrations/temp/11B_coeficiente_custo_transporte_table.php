<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coeficiente_custo_transporte', function (Blueprint $table) {
            $table->id();
            $table->date('data_base');
            $table->string('desoneracao', 3);
            $table->unsignedBigInteger('custo_transporte_id');
            $table->decimal('coeficiente_x1', 12, 4);
            $table->decimal('coeficiente_x2', 12, 4);
            $table->decimal('termo_independente', 12, 4);
            $table->timestamps();

            $table->foreign('custo_transporte_id')
                ->references('id')->on('custo_transporte')
                ->onDelete('cascade');

            $table->unique(['data_base', 'desoneracao', 'custo_transporte_id'], 'coef_transporte_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coeficiente_custo_transporte');
    }
}; 