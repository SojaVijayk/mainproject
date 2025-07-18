<?php

namespace App\Exports;

use App\Models\MaintenanceRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaintenanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $maintenance;

    public function __construct($maintenance)
    {
        $this->maintenance = $maintenance;
    }

    public function collection()
    {
        return $this->maintenance;
    }

    public function headings(): array
    {
        return [
            'Asset',
            'Title',
            'Details',
            'Start Date',
            'Completion Date',
            'Cost',
            'Status',
            'Created By'
        ];
    }

    public function map($record): array
    {
        return [
            $record->asset->asset_tag . ' - ' . $record->asset->name,
            $record->title,
            $record->details,
            $record->start_date->format('Y-m-d'),
            $record->completion_date ? $record->completion_date->format('Y-m-d') : '',
            $record->cost,
            $record->status,
            $record->user->name
        ];
    }
}
