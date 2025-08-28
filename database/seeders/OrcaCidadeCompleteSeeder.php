<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Importando os seeders das pastas organizadas
use Database\Seeders\Iniciais\Autenticacao\SetupUsuariosPermissoesSeeder;
use Database\Seeders\Iniciais\TabelasOficiais\DerprSeeder;
use Database\Seeders\Iniciais\TabelasOficiais\SinapiSeeder;
use Database\Seeders\Iniciais\TabelasOficiais\ConsultarDerprSeeder;
use Database\Seeders\Iniciais\TabelasOficiais\ConsultarSinapiSeeder;
use Database\Seeders\Iniciais\Administracao\EstruturaOrcamentoSeeder;
use Database\Seeders\Iniciais\Administracao\MunicipiosSeeder;
use Database\Seeders\Iniciais\Administracao\EntidadesOrcamentariasSeeder;
use Database\Seeders\Iniciais\Administracao\UsuariosEntidadesSeeder;

class OrcaCidadeCompleteSeeder extends Seeder
{
    /**
     * ===== SEEDER DAS CONFIGURAÇÕES INICIAIS DO SISTEMA =====
     * 
     * Este seeder executa APENAS as configurações fundamentais e prerequisitos
     * do sistema OrçaCidade. Ele deve ser executado ANTES de qualquer módulo
     * específico do sistema.
     * 
     * ESTRUTURA ORGANIZADA:
     * - Iniciais/Autenticacao/      → Usuários, papéis, permissões
     * - Iniciais/TabelasOficiais/   → DERPR, SINAPI e consultas
     * - Iniciais/Administracao/     → Estrutura, municípios, entidades
     * 
     * MÓDULOS ESPECÍFICOS (separados):
     * - Orcamento/                  → Seeders do módulo de orçamento
     * 
     * ORDEM DE EXECUÇÃO DOS INICIAIS:
     * 1. Base de autenticação e permissões
     * 2. Tabelas oficiais (DER-PR e SINAPI)
     * 3. Estrutura orçamentária básica
     * 4. Administração (municípios, entidades, usuários)
     */
    public function run(): void
    {
        $this->command->info('🚀 INICIANDO SETUP DAS CONFIGURAÇÕES INICIAIS - ORÇACIDADE');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');
        
        $seeders = [
            [
                'step' => '1️⃣',
                'module' => 'Sistema Base',
                'description' => 'Usuários, papéis e permissões fundamentais',
                'class' => SetupUsuariosPermissoesSeeder::class
            ],
            [
                'step' => '2️⃣',
                'module' => 'DER-PR (Importação)',
                'description' => 'Permissões para importação de dados DER-PR',
                'class' => DerprSeeder::class
            ],
            [
                'step' => '3️⃣',
                'module' => 'SINAPI (Importação)',
                'description' => 'Permissões para importação de dados SINAPI',
                'class' => SinapiSeeder::class
            ],
            [
                'step' => '4️⃣',
                'module' => 'Estrutura Orçamentária',
                'description' => 'Tipos, grandes itens e sub grupos orçamentários',
                'class' => EstruturaOrcamentoSeeder::class
            ],
            [
                'step' => '5️⃣',
                'module' => 'DER-PR (Consulta)',
                'description' => 'Permissões para consulta de tabelas DER-PR',
                'class' => ConsultarDerprSeeder::class
            ],
            [
                'step' => '6️⃣',
                'module' => 'SINAPI (Consulta)',
                'description' => 'Permissões para consulta de tabelas SINAPI',
                'class' => ConsultarSinapiSeeder::class
            ],
            [
                'step' => '7️⃣',
                'module' => 'Municípios',
                'description' => 'Gestão e importação de municípios',
                'class' => MunicipiosSeeder::class
            ],
            [
                'step' => '8️⃣',
                'module' => 'Entidades Orçamentárias',
                'description' => 'Gestão de entidades orçamentárias',
                'class' => EntidadesOrcamentariasSeeder::class
            ],
            [
                'step' => '9️⃣',
                'module' => 'Usuários por Entidade',
                'description' => 'Vinculação e aprovação de usuários por entidade',
                'class' => UsuariosEntidadesSeeder::class
            ]
        ];
        
        $totalSeeders = count($seeders);
        $startTime = now();
        
        foreach ($seeders as $index => $seederInfo) {
            $currentStep = $index + 1;
            
            $this->command->info("🔄 EXECUTANDO {$seederInfo['step']} {$seederInfo['module']}");
            $this->command->line("   📋 {$seederInfo['description']}");
            $this->command->line("   🏷️  Classe: {$seederInfo['class']}");
            $this->command->line("   📊 Progresso: {$currentStep}/{$totalSeeders}");
            $this->command->info('   ⏱️  Iniciando...');
            
            $seederStartTime = now();
            
            try {
                $this->call($seederInfo['class']);
                
                $seederDuration = $seederStartTime->diffForHumans(now(), true);
                $this->command->info("   ✅ CONCLUÍDO em {$seederDuration}");
                
            } catch (\Exception $e) {
                $this->command->error("   ❌ ERRO: {$e->getMessage()}");
                $this->command->error("   🛑 Execução interrompida no seeder: {$seederInfo['class']}");
                throw $e;
            }
            
            $this->command->info('');
        }
        
        $totalDuration = $startTime->diffForHumans(now(), true);
        
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('🎉 SETUP DAS CONFIGURAÇÕES INICIAIS FINALIZADO COM SUCESSO!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info("⏱️  Tempo total: {$totalDuration}");
        $this->command->info("📊 Seeders executados: {$totalSeeders}/{$totalSeeders}");
        $this->command->info('');
        
        $this->showSystemSummary();
    }
    
    /**
     * Mostra um resumo do que foi configurado no sistema
     */
    private function showSystemSummary(): void
    {
        $this->command->info('📋 RESUMO DO SISTEMA CONFIGURADO:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $this->command->info('🔐 AUTENTICAÇÃO E PERMISSÕES:');
        $this->command->line('   • Sistema de usuários, papéis e permissões');
        $this->command->line('   • Usuário super administrador criado');
        $this->command->line('   • Autenticação baseada em sessão');
        $this->command->line('');
        
        $this->command->info('📊 TABELAS OFICIAIS:');
        $this->command->line('   • DER-PR: Importação e consulta configuradas');
        $this->command->line('   • SINAPI: Importação e consulta configuradas');
        $this->command->line('   • Permissões específicas por funcionalidade');
        $this->command->line('');
        
        $this->command->info('🏗️ ESTRUTURA ORÇAMENTÁRIA:');
        $this->command->line('   • Tipos de orçamentos');
        $this->command->line('   • Grandes itens orçamentários');
        $this->command->line('   • Sub grupos de classificação');
        $this->command->line('');
        
        $this->command->info('🏛️ ADMINISTRAÇÃO:');
        $this->command->line('   • Gestão de municípios');
        $this->command->line('   • Gestão de entidades orçamentárias');
        $this->command->line('   • Vinculação de usuários por entidade');
        $this->command->line('   • Sistema de aprovação de cadastros');
        $this->command->line('');
        
        $this->command->info('🎯 PRÓXIMOS PASSOS:');
        $this->command->line('   1. Executar seeders do módulo ORÇAMENTO');
        $this->command->line('   2. Configurar usuários de teste');
        $this->command->line('   3. Testar funcionalidades básicas');
        $this->command->line('   4. Validar fluxos fundamentais');
        $this->command->line('');
        
        $this->command->info('✅ Base do sistema OrçaCidade configurada!');
        $this->command->info('🔄 Pronto para módulos específicos!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
