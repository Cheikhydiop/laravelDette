<?php
namespace App\Observers;

use App\Models\Dette;
use App\Services\StockService;

class DetteObserver
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Handle the Dette "created" event.
     *
     * @param  \App\Models\Dette  $dette
     * @return void
     */
    public function created(Dette $dette)
    {
        $this->stockService->handleCreation($dette);
    }

    /**
     * Handle the Dette "updated" event.
     *
     * @param  \App\Models\Dette  $dette
     * @return void
     */
    public function updated(Dette $dette)
    {
        $this->stockService->handleUpdate($dette);
    }
}
