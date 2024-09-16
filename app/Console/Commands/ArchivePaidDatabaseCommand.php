<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\DatabaseController; // Correction du nom de la classe

class ArchivePaidDatabaseCommand extends Command
{
    protected $signature = 'debts:archive2';
    protected $description = 'Archive paid debts and store them in the selected database';

    protected $databaseController;

    // public function __construct(DatabaseController $databaseController) // Correction du nom de la classe
    // {
    //     parent::__construct();
    //     $this->databaseController = $databaseController;
    // }

    public function handle()
    {
        try {
            // $response = $this->databaseController->archivePaidDebts();
            $this->info($response->getContent());
        } catch (\Exception $e) {
            $this->error('Failed to archive paid debts: ' . $e->getMessage());
        }
    }
}
