<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar usuários no sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all(['id', 'name', 'email', 'is_active', 'password']);
        
        if ($users->isEmpty()) {
            $this->error('Nenhum usuário encontrado no sistema!');
            return 1;
        }

        $this->info('Usuários encontrados:');
        $this->table(
            ['ID', 'Nome', 'Email', 'Ativo', 'Tem Senha'],
            $users->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->is_active ? 'Sim' : 'Não',
                    !empty($user->password) ? 'Sim' : 'Não'
                ];
            })
        );

        return 0;
    }
}
