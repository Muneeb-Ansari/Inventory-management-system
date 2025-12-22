<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Location;

class StockMovementExport implements 
    FromCollection, 
    WithHeadings, 
    WithStyles, 
    WithTitle, 
    WithEvents,
    WithColumnWidths,
    WithMapping
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
        return $this->data;
    }

    public function map($movement): array
    {
        $reference = '';
        if ($movement->movementable) {
            if ($movement->movement_type === 'purchase') {
                $reference = $movement->movementable->purchase_no ?? 'N/A';
            } elseif ($movement->movement_type === 'sale') {
                $reference = $movement->movementable->sale_no ?? 'N/A';
            } else {
                $reference = ucfirst($movement->movement_type);
            }
        }

        return [
            $movement->movement_date->format('d-M-Y'),
            $movement->product->name,
            $movement->product->code,
            $movement->location->name,
            ucfirst($movement->movement_type),
            $reference,
            $movement->quantity_in > 0 ? number_format($movement->quantity_in, 2) : '-',
            $movement->quantity_out > 0 ? number_format($movement->quantity_out, 2) : '-',
            number_format($movement->rate, 2),
            number_format($movement->balance, 2),
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Product Name',
            'Product Code',
            'Location',
            'Type',
            'Reference',
            'Inward',
            'Outward',
            'Rate',
            'Balance',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Date
            'B' => 30,  // Product Name
            'C' => 15,  // Product Code
            'D' => 20,  // Location
            'E' => 12,  // Type
            'F' => 18,  // Reference
            'G' => 12,  // Inward
            'H' => 12,  // Outward
            'I' => 12,  // Rate
            'J' => 12,  // Balance
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            4 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D3D3D3']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Stock Movements';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $location = Location::find($this->locationId);
                
                // Insert header rows
                $sheet->insertNewRowBefore(1, 3);
                
                $sheet->setCellValue('A1', 'Stock Movement Register');
                $sheet->setCellValue('A2', 'Location: ' . ($location->name ?? 'All Locations'));
                $sheet->setCellValue('A3', 'Period: ' . \Carbon\Carbon::parse($this->startDate)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($this->endDate)->format('d M Y'));
                
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A2:A3')->getFont()->setBold(true)->setSize(11);
                
                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A2:J2');
                $sheet->mergeCells('A3:J3');
                
                $sheet->getStyle('A1:J3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $lastRow = $sheet->getHighestRow();
                
                // Apply borders
                $sheet->getStyle('A4:J' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                
                // Right align numeric columns
                $numericColumns = ['G', 'H', 'I', 'J'];
                foreach ($numericColumns as $column) {
                    $sheet->getStyle($column . '5:' . $column . $lastRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
                
                // Add totals row
                $totalRow = $lastRow + 1;
                $sheet->setCellValue('A' . $totalRow, 'Total:');
                $sheet->mergeCells('A' . $totalRow . ':F' . $totalRow);
                
                $sheet->setCellValue('G' . $totalRow, '=SUMIF(G5:G' . $lastRow . ',"<>-")');
                $sheet->setCellValue('H' . $totalRow, '=SUMIF(H5:H' . $lastRow . ',"<>-")');
                
                $sheet->getStyle('A' . $totalRow . ':J' . $totalRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFF00']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            },
        ];
    }
}