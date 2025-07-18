<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $assets;

    public function __construct($assets)
    {
        $this->assets = $assets;
    }

    public function collection()
    {
        return $this->assets;
    }

    public function headings(): array
    {
        return [
            'Asset Tag',
            'Name',
            'Model',
            'Category',
            'Status',
            'Assigned To',
            'Location',
            'Purchase Cost',
            'Purchase Date',
            'Warranty Expiry',
            'Serial Number'
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->asset_tag,
            $asset->name,
            $asset->model->name,
            $asset->model->category->name,
            $asset->status->name,
            $asset->assigned_to,
            $asset->location->name,
            $asset->purchase_cost,
            $asset->purchase_date->format('Y-m-d'),
            $asset->warranty_expiry ? $asset->warranty_expiry->format('Y-m-d') : '',
            $asset->serial_number
        ];
    }
}
