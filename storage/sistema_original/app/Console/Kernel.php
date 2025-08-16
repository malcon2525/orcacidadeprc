<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Sincronização automática do Active Directory
        // Configurações podem ser alteradas via interface web em /administracao/active-directory
        $schedule->job(new \App\Jobs\SyncActiveDirectoryJob())
                ->daily()
                ->at('02:00')
                ->withoutOverlapping()
                ->runInBackground()
                ->onFailure(function () {
                    Log::error('Sincronização automática AD falhou');
                });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }


}
