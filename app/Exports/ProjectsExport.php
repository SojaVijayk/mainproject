<?php

namespace App\Exports;

use App\Models\PMS\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class ProjectsExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Project::with(['requirement.client', 'investigator', 'expenses', 'invoices.payments']);

        // Filters
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }
        if ($this->request->filled('investigator_id')) {
            $query->where('project_investigator_id', $this->request->investigator_id);
        }
        if ($this->request->filled('client_id')) {
            $query->whereHas('requirement', function ($q) {
                $q->where('client_id', $this->request->client_id);
            });
        }

        $projects = $query->get();

        return $projects->map(function ($project) {
            $budget = $project->budget ?? 0;
            $expenses = $project->expenses->sum('total_amount');
            $totalInvoiced = $project->invoices->whereIn('status', [\App\Models\PMS\Invoice::STATUS_SENT, \App\Models\PMS\Invoice::STATUS_PAID])->sum('total_amount');
            $totalPaid = $project->invoices->sum(function ($invoice) {
                return $invoice->payments->sum('amount');
            });

            return [
                'Project Title' => $project->title,
                'Project Code' => $project->project_code,
                'Client' => $project->requirement->client->name ?? 'N/A',
                'Investigator' => $project->investigator->name ?? 'N/A',
                'Status' => $project->status_name,
                'Budget' => $budget,
                'Expenses' => $expenses,
                'Remaining Budget' => $budget - $expenses,
                'Budget Utilization %' => $budget > 0 ? round(($expenses / $budget) * 100, 2) : 0,
                'Invoiced' => $totalInvoiced,
                'Paid' => $totalPaid,
                'Outstanding' => $totalInvoiced - $totalPaid,
                'Completion %' => $project->completion_percentage,
                'Start Date' => $project->start_date?->format('Y-m-d'),
                'End Date' => $project->end_date?->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Project Title', 'Project Code', 'Client', 'Investigator', 'Status',
            'Budget', 'Expenses', 'Remaining Budget', 'Budget Utilization %',
            'Invoiced', 'Paid', 'Outstanding', 'Completion %', 'Start Date', 'End Date'
        ];
    }
}
