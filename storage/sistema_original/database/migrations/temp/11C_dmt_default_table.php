<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dmt_default', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_material', 10);
            $table->string('nome_material', 255);
            $table->string('origem', 150)->nullable();
            $table->string('destino', 150)->nullable();
            $table->string('sigla_transporte', 3);
            $table->enum('tipo', ['local', 'comercial']);
            $table->decimal('x1', 10, 2)->default(0);
            $table->decimal('x2', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiais_transporte');
    }
};