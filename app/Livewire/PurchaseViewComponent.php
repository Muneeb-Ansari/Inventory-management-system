<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Purchase,Inventory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PurchaseViewComponent extends Component
{
    use AuthorizesRequests;

    public $purchaseId;
    public $purchase;

    public function mount($id)
    {
        // $this->authorize('viewAny', Inventory::class);

        $this->purchaseId = $id;
        $this->purchase = Purchase::with(['location', 'user', 'items.product'])
            ->findOrFail($this->purchaseId);

    }

    public function downloadPDF()
    {
        $this->authorize('view', Inventory::class);

        $pdf = Pdf::loadView('reports.purchase-pdf', [
            'purchase' => $this->purchase,
            'location'   => $this->purchase->name ?? '',
            'startDate'  => $this->purchase->purchase_date ?? now()->startOfMonth(),
            'endDate'    => $this->endDate ?? now()->endOfMonth(),
            'data'       => $this->purchase->items ?? [],
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "purchase_{$this->purchase->purchase_no}.pdf");
    }

    public function printPurchase()
    {
        $this->dispatch('print-purchase');
    }

    public function render()
    {
        return view('livewire.purchase-view-component')->layout('layouts.app');
    }
}
