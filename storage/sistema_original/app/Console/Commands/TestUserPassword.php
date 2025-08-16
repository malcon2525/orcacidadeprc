<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserPassword extends Command
{
    protected $signature = 'user:test-password {email} {password}';
    protected $description = 'Testa se uma senha corresponde ao hash do usuário';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuário não encontrado: {$email}");
            return 1;
        }
        
        $this->info("=== TESTE DE SENHA ===");
        $this->line("Usuário: {$user->name} ({$email})");
        $this->line("Hash atual: {$user->password}");
        $this->line("Senha testada: {$password}");
        $this->line("Tamanho da senha: " . strlen($password));
        
        $isValid = Hash::check($password, $user->password);
        
        $this->line("Resultado: " . ($isValid ? '✅ SENHA CORRETA' : '❌ SENHA INCORRETA'));
        
        if (!$isValid) {
            $this->warn("A senha '{$password}' não corresponde ao hash armazenado.");
            $this->line("Possíveis causas:");
            $this->line("- Senha diferente da que está no AD");
            $this->line("- Hash corrompido durante sincronização");
            $this->line("- Senha foi alterada manualmente");
        }
        
        return 0;
    }
} 