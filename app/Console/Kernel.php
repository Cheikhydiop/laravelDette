<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Example: Schedule a command to run daily at midnight
        // $schedule->command('inspire')->daily();
        $schedule->command('email:send-debt-reminder')->fridays()->at('14:00');
    
    //    $schedule->command('debts:archive')->everyMinute();
    //    $schedule->command('debts:send-total')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        
        // Register your commands here
        
        require base_path('routes/console.php');
    }
    
    protected $commands = [
        \App\Console\Commands\ArchivePaidDebts::class,
        \App\Console\Commands\SendTotalDebts::class,
        \App\Console\Commands\ArchivePaidDatabaseCommand::class,

    ];
  
    
}
