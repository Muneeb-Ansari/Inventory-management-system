<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Location;
use App\Repositories\InventoryRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\InventoryReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Policies\InventoryPolicy;
use Illuminate\Pagination\LengthAwarePaginator;


class InventoryReportComponent extends Component
{
    use WithPagination, AuthorizesRequests;

    public $locationId;
    public $startDate;
    public $endDate;
    public $searchProduct = '';
    public $perPage = 50;

    public $locations;
    public $reportData = [];
    public $showReport = false;

    protected $queryString = [
        'locationId',
        'startDate',
        'endDate',
        'searchProduct' => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('viewReport', InventoryPolicy::class);

        $this->locations = Location::active()->get();
        $this->locationId = $this->locations->first()?->id;
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function generateReport(InventoryRepository $repository)
    {
        $this->validate([
            'locationId' => 'required|exists:locations,id',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        $this->reportData = $repository->getInventoryReport(
            $this->locationId,
            $this->startDate,
            $this->endDate
        )->filter(function ($item) {
            if (empty($this->searchProduct)) {
                return true;
            }
            return str_contains(strtolower($item['product_name']), strtolower($this->searchProduct)) ||
                str_contains(strtolower($item['product_code']), strtolower($this->searchProduct));
        })->values();

        $this->showReport = true;
    }

    public function downloadPDF()
    {
        $this->authorize('downloadReport', InventoryPolicy::class);

        $location = Location::find($this->locationId);

        $pdf = Pdf::loadView('reports.inventory-pdf', [
            'data' => $this->reportData,
            'location' => $location,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "inventory_report_{$this->locationId}_{$this->startDate}.pdf");
    }

    public function downloadExcel()
    {
        $this->authorize('downloadReport', InventoryPolicy::class);

        return Excel::download(
            new InventoryReportExport($this->reportData, $this->locationId, $this->startDate, $this->endDate),
            "inventory_report_{$this->locationId}_{$this->startDate}.xlsx"
        );
    }

    public function updatedSearchProduct()
    {
        $this->generateReport(app(InventoryRepository::class));
    }

    public function render()
    {
        $collection = collect($this->reportData);

        // Manual pagination
        $currentPage = request()->get('page', 1);
        $perPage = $this->perPage;

        $paginatedData = new LengthAwarePaginator(
            $collection->forPage($currentPage, $perPage),
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.inventory-report-component', [
            'paginatedData' => $paginatedData,
        ])->layout('layouts.app');
    }
}
