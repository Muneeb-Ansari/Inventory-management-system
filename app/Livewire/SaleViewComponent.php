<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SaleViewComponent extends Component
{
    use AuthorizesRequests;

    public $saleId;
    public $sale;

    public function mount($id)
    {
        $this->authorize('viewAny', Inventory::class);

        $this->saleId = $id;
        $this->sale = Sale::with(['location', 'user', 'items.product'])
            ->findOrFail($id);
    }

    public function downloadPDF()
    {
        $this->authorize('view', InventoryPolicy::class);

        $pdf = Pdf::loadView('reports.sale-pdf', [
            'sale' => $this->sale,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, "sale_{$this->sale->sale_no}.pdf");
    }

    public function render()
    {
        return view('livewire.sale-view-component')->layout('layouts.app');
    }
}
