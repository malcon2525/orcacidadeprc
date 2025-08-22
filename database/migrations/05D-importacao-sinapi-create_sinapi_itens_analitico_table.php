<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sinapi_itens_analitico', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_composicao');
            $table->string('tipo_item');
            $table->string('codigo_item');
            $table->text('descricao');
            $table->string('unidade')->nullable();
            $table->string('coeficiente')->nullable();
            $table->string('situacao')->nullable();
            $table->date('data_base');
            $table->date('data_emissao')->nullable();
            $table->timestamps();

            $table->unique([
                'codigo_composicao',
                'codigo_item',
                'data_base',
            ], 'item_analitico_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinapi_itens_analitico');
    }
};
