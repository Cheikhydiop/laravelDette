<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DetteService;

class ArchivePaidDebts extends Command
{
    protected $signature = 'debts:archive';
    protected $description = 'Archive paid debts';

    protected $detteService;

    public function __construct(DetteService $detteService)
    {
        parent::__construct();
        $this->detteService = $detteService;
    }

    public function handle()
    {
        $this->detteService->archivePaidDebts();
        $this->info('Dettes payées archivées avec succès');
    }
}
