<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sub_grupos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grande_item_id');
            $table->string('descricao', 100);
            $table->integer('ordem')->default(0);
            $table->timestamps();

            // Índices e chaves
            $table->foreign('grande_item_id')
                  ->references('id')
                  ->on('grandes_itens')
                  ->onDelete('cascade');
            $table->index('ordem');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_grupos');
    }
}; 