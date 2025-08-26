<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Administracao\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Criar a nova permissão para aprovação de cadastros
        Permission::create([
            'name' => 'aprovar-cadastros',
            'display_name' => 'Aprovar Cadastros de Usuários',
            'description' => 'Permite aprovar/rejeitar solicitações de cadastro e vincular usuários a entidades orçamentárias'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover a permissão criada
        Permission::where('name', 'aprovar-cadastros')->delete();
    }
};
