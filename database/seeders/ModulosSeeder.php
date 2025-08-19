<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModulosSeeder extends Seeder
{
    /**
     * Execute the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando setup de todos os módulos...');
        
        // Executar seeders de módulos específicos
        $this->call([
            MunicipiosSeeder::class,
            EntidadesOrcamentariasSeeder::class,
            // Adicionar outros seeders de módulos aqui conforme necessário
        ]);
        
        $this->command->info('✅ Setup de todos os módulos concluído com sucesso!');
        $this->command->info('🎯 Agora você pode atribuir os papéis aos usuários conforme necessário.');
    }
}
