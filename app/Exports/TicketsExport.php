<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tickets;

    public function __construct($tickets)
    {
        $this->tickets = $tickets;
    }

    public function collection()
    {
        return $this->tickets;
    }

    public function headings(): array
    {
        return [
            'Ticket Number',
            'Title',
            'Description',
            'Priority',
            'Status',
            'Asset',
            'Created By',
            'Assigned To',
            'Created At',
            'Resolved At',
            'Resolution'
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->ticket_number,
            $ticket->title,
            $ticket->description,
            $ticket->priority,
            $ticket->status,
            $ticket->asset ? $ticket->asset->asset_tag . ' - ' . $ticket->asset->name : '',
            $ticket->user->name,
            $ticket->assignedTo ? $ticket->assignedTo->name : '',
            $ticket->created_at->format('Y-m-d H:i'),
            $ticket->resolved_at ? $ticket->resolved_at->format('Y-m-d H:i') : '',
            $ticket->resolution
        ];
    }
}
