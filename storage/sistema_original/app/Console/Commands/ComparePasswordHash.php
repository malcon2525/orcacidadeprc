<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ComparePasswordHash extends Command
{
    protected $signature = 'hash:compare {password} {hash}';
    protected $description = 'Gera hash de uma senha e compara com um hash existente';

    public function handle()
    {
        $password = $this->argument('password');
        $existingHash = $this->argument('hash');
        
        $this->info("=== COMPARAÇÃO DE HASH ===");
        $this->line("Senha: {$password}");
        $this->line("Hash existente: {$existingHash}");
        
        // Gerar novo hash para a senha
        $newHash = Hash::make($password);
        $this->line("Novo hash gerado: {$newHash}");
        
        // Verificar se a senha corresponde ao hash existente
        $isValid = Hash::check($password, $existingHash);
        
        $this->line("");
        $this->line("=== RESULTADO ===");
        $this->line("Hash::check('{$password}', '{$existingHash}') = " . ($isValid ? 'true' : 'false'));
        
        if ($isValid) {
            $this->info("✅ A senha corresponde ao hash existente!");
        } else {
            $this->error("❌ A senha NÃO corresponde ao hash existente!");
            $this->line("");
            $this->warn("Possíveis causas:");
            $this->line("- A senha no AD é diferente de '{$password}'");
            $this->line("- O hash foi gerado com salt diferente");
            $this->line("- Problema na sincronização do AD");
        }
        
        return 0;
    }
} 