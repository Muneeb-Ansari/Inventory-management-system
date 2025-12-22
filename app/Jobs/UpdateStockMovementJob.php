<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Repositories\InventoryRepository;
use Illuminate\Support\Facades\Log;

class UpdateStockMovementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $model;
    public $type;

    public function __construct($model, $type)
    {
        $this->model = $model;
        $this->type = $type;
    }

    public function handle(InventoryRepository $repository)
    {
        $repository->updateStockMovement($this->type, $this->model);
    }

    public function failed(\Throwable $exception)
    {
        \Log::error("Stock movement update failed: " . $exception->getMessage());
    }
}
