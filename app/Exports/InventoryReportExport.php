<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Location;

class InventoryReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithEvents
{
    protected $data;
    protected $locationId;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $locationId, $startDate, $endDate)
    {
        $this->data = $data;
        $this->locationId = $locationId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return collect($this->data)->map(function($item, $index) {
            return [
                'sr_no' => $index + 1,
                'product_name' => $item['product_name'],
                'product_code' => $item['product_code'],
                'opening_qty' => $item['opening_qty'],
                'opening_rate' => $item['opening_rate'],
                'opening_amount' => $item['opening_amount'],
                'purchase_qty' => $item['purchase_qty'],
                'purchase_rate' => $item['purchase_rate'],
                'purchase_amount' => $item['purchase_amount'],
                'sale_qty' => $item['sale_qty'],
                'sale_rate' => $item['sale_rate'],
                'sale_amount' => $item['sale_amount'],
                'closing_qty' => $item['closing_qty'],
                'closing_rate' => $item['closing_rate'],
                'closing_amount' => $item['closing_amount'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Sr #',
            'Product Name',
            'Product Code',
            'Opening Qty',
            'Opening Rate',
            'Opening Amount',
            'Purchase Qty',
            'Purchase Rate',
            'Purchase Amount',
            'Sale Qty',
            'Sale Rate',
            'Sale Amount',
            'Closing Qty',
            'Closing Rate',
            'Closing Amount',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Inventory Report';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $location = Location::find($this->locationId);

                $event->sheet->insertNewRowBefore(1, 3);
                $event->sheet->setCellValue('A1', 'Inventory Control Register');
                $event->sheet->setCellValue('A2', 'Location: ' . $location->name);
                $event->sheet->setCellValue('A3', 'Period: ' . $this->startDate . ' to ' . $this->endDate);

                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $event->sheet->getStyle('A2:A3')->getFont()->setBold(true);
            },
        ];
    }
}
