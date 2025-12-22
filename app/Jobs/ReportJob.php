<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Repositories\InventoryRepository;
use App\Exports\InventoryReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GenerateInventoryReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $locationId;
    public $startDate;
    public $endDate;
    public $userId;
    public $format;

    public function __construct($locationId, $startDate, $endDate, $userId, $format = 'excel')
    {
        $this->locationId = $locationId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userId = $userId;
        $this->format = $format;
    }

    public function handle(InventoryRepository $repository)
    {
        $data = $repository->getInventoryReport(
            $this->locationId,
            $this->startDate,
            $this->endDate
        );

        $filename = "inventory_report_{$this->locationId}_{$this->startDate}_{$this->endDate}.xlsx";

        Excel::store(
            new InventoryReportExport($data, $this->locationId, $this->startDate, $this->endDate),
            $filename,
            'public'
        );

        // Notify user that report is ready
        // You can use Laravel notifications here
        \Log::info("Report generated: {$filename}");
    }
}
