<?php

namespace App\Exports;

use App\Models\Vending;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VendingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Vending::with(['item', 'user'])->get();
    }

    public function headings(): array
    {
        return [
            'Device',
            'Item',
            'Name',
            'Role',
            'Date',
        ];
    }

    public function map($record): array
    {
        return [
            $record->device,
            $record->item->name ?? '',
            $record->user->name ?? '',
            $record->user->role ?? '',
            $record->created_at->toDateTimeString(),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header styling
            1 => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFDDDDDD']
            ]],
            // Optional column widths
            'A' => ['width' => 20],
            'B' => ['width' => 20],
            'C' => ['width' => 25],
            'D' => ['width' => 15],
            'E' => ['width' => 25],
        ];
    }
}

