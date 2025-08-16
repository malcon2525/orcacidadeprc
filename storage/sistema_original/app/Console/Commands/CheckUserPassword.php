<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserPassword extends Command
{
    protected $signature = 'user:check {email}';
    protected $description = 'Verifica informações do usuário e senha';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuário não encontrado: {$email}");
            return 1;
        }
        
        $this->info("=== INFORMAÇÕES DO USUÁRIO ===");
        $this->line("ID: {$user->id}");
        $this->line("Nome: {$user->name}");
        $this->line("Email: {$user->email}");
        $this->line("Login Type: {$user->login_type}");
        $this->line("Ativo: " . ($user->is_active ? 'Sim' : 'Não'));
        $this->line("Tem senha: " . (!empty($user->password) ? 'Sim' : 'Não'));
        
        if (!empty($user->password)) {
            $this->line("Hash da senha: {$user->password}");
            $this->line("Primeiros 20 chars: " . substr($user->password, 0, 20));
            $this->line("Últimos 20 chars: " . substr($user->password, -20));
        }
        
        return 0;
    }
} 