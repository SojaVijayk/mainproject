<?php

namespace App\Http\Controllers\PMS;

use App\Http\Controllers\Controller;
use App\Models\PMS\Project;
use App\Models\PMS\Timesheet;
use App\Models\PMS\Invoice;
use App\Models\PMS\Proposal;
use App\Models\PMS\Requirement;
use App\Models\User;
use App\Models\Client;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function projectStatus(Request $request)
    {
      $pageConfigs = ['myLayout' => 'horizontal'];
       $user = Auth::user();
        $statusFilter = $request->input('status', 'all');
        $dateRange = $request->input('date_range', 'this_year');

        $query = Project::query();

          if (!$user->hasRole('director') && !$user->hasRole('finance')) {

            $investigatorId =$user->id;
               $query->where('project_investigator_id', $investigatorId);
          }

        // Apply status filter
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Apply date range filter
        $query = $this->applyDateRangeFilter($query, $dateRange);

        $projects = $query->with(['requirement.client', 'proposal'])->get();

        $statuses = [
            'all' => 'All Statuses',
            Project::STATUS_INITIATED => 'Initiated',
            Project::STATUS_ONGOING => 'Ongoing',
            Project::STATUS_COMPLETED => 'Completed'
        ];

        $dateRanges = [
            'this_week' => 'This Week',
            'this_month' => 'This Month',
            'this_quarter' => 'This Quarter',
            'this_year' => 'This Year',
            'last_week' => 'Last Week',
            'last_month' => 'Last Month',
            'last_quarter' => 'Last Quarter',
            'last_year' => 'Last Year',
            'custom' => 'Custom Range'
        ];

        return view('pms.reports.project-status', compact('projects', 'statuses', 'statusFilter', 'dateRanges', 'dateRange'),['pageConfigs'=> $pageConfigs]);
    }
    public function projectStatusDetailed(Request $request)
{
  $pageConfigs = ['myLayout' => 'horizontal'];

  $user = Auth::user();

    $categoryId = $request->input('category_id');
       if ($user->hasRole('director') || $user->hasRole('finance')) {
    $investigatorId = $request->input('investigator_id');
       }
       else {
           $investigatorId =$user->id;
       }
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');


      $liveProjects = Project::where('status', Project::STATUS_ONGOING)
         ->when($investigatorId, fn($q) => $q->where('project_investigator_id', $investigatorId))
        ->when($startDate && $endDate, fn($q) => $q->whereBetween('start_date', [$startDate, $endDate]))
        ->when($categoryId, fn($q) => $q->whereHas('requirement', fn($r) => $r->where('project_category_id', $categoryId)))
        ->count();

         $completedProjects = Project::where('status', Project::STATUS_COMPLETED)
         ->when($investigatorId, fn($q) => $q->where('project_investigator_id', $investigatorId))
        ->when($startDate && $endDate, fn($q) => $q->whereBetween('start_date', [$startDate, $endDate]))
        ->when($categoryId, fn($q) => $q->whereHas('requirement', fn($r) => $r->where('project_category_id', $categoryId)))
        ->count();
      $ongoingProjects = Project::where('status', Project::STATUS_ONGOING)
         ->when($investigatorId, fn($q) => $q->where('project_investigator_id', $investigatorId))
        ->when($startDate && $endDate, fn($q) => $q->whereBetween('start_date', [$startDate, $endDate]))
        ->when($categoryId, fn($q) => $q->whereHas('requirement', fn($r) => $r->where('project_category_id', $categoryId)))
        ->count();

        $delayedProjects = Project::where('status', Project::STATUS_ONGOING)
    ->where('end_date', '<', Carbon::today()) // ✅ ongoing projects with end_date in future
    ->when($investigatorId, fn($q) => $q->where('project_investigator_id', $investigatorId))
    ->when($startDate && $endDate, fn($q) => $q->whereBetween('start_date', [$startDate, $endDate]))
    ->when($categoryId, fn($q) => $q->whereHas('requirement', fn($r) => $r->where('project_category_id', $categoryId)))
    ->count();

     $archived = Project::where('status', Project::STATUS_ARCHIVED)
         ->when($investigatorId, fn($q) => $q->where('project_investigator_id', $investigatorId))
        ->when($startDate && $endDate, fn($q) => $q->whereBetween('start_date', [$startDate, $endDate]))
        ->when($categoryId, fn($q) => $q->whereHas('requirement', fn($r) => $r->where('project_category_id', $categoryId)))
        ->count();


        // Proposal Submitted
        $proposalSubmitted_query = Proposal::where('project_status', 0)
         ->when($investigatorId, fn($q) => $q->where('created_by', $investigatorId))
         ->when($categoryId, fn($q) => $q->whereHas('requirement', fn($r) => $r->where('project_category_id', $categoryId)))
        ->when($startDate && $endDate, fn($q) => $q->whereBetween('expected_start_date', [$startDate, $endDate]))
            // ->whereNull('client_status')
            ->where('project_status',0)
            ->get();
             $proposalSubmitted =  $proposalSubmitted_query->count();

            // Grouping by category
    $categoryWise_proposalSubmitted = $proposalSubmitted_query->groupBy(fn($p) => $p->requirement->category->name ?? 'Uncategorized');

    $categorySummary_proposalSubmitted = $categoryWise_proposalSubmitted->map(function ($group) {
        return [
            'total_budget' => $group->sum('budget')/100000,
            'total_revenue' => $group->sum('revenue')/100000,
            'Accepted' => $group->where('client_status', 'accepted')->count(),
            'Rejected' => $group->where('client_status', 'rejected')->count(),
            'resubmit_requested' => $group->where('client_status', 'resubmit_requested')->count(),
            'submitted' => $group->whereNull('client_status')->count(),
        ];
    });

        // Planning Stage
        $planningStage_query = Requirement::where('proposal_status', 0)
          ->when($investigatorId, fn($q) => $q->where('created_by', $investigatorId))
            ->when($categoryId, fn($q) => $q->where('project_category_id', $categoryId))
        ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
        ->get();
          $planningStage = $planningStage_query->count();
      $categoryWise_planningStage = $planningStage_query->groupBy(fn($p) => $p->category->name ?? 'Uncategorized');

      $categorySummary_planningStage = $categoryWise_planningStage->map(function ($group) {
        return [
            'created' => $group->where('status', 0)->count(),
            'submitted' => $group->where('status', 1)->count(),
            'under_pac' => $group->where('status', 4)->count(),
            'rejected' => $group->where('status',3)->count(),
              'approved' => $group->whereNotNull('allocated_to')->count(),
        ];
    });

    // Base query with relationships
    $projects = Project::with(['requirement.category', 'proposal', 'invoices.payments', 'investigator'])
        ->when($categoryId, fn($q) => $q->whereHas('requirement', fn($r) => $r->where('project_category_id', $categoryId)))
        ->when($investigatorId, fn($q) => $q->where('project_investigator_id', $investigatorId))
        ->when($startDate && $endDate, fn($q) => $q->whereBetween('start_date', [$startDate, $endDate]))
        ->get();

    // Grouping by category
    $categoryWise = $projects->groupBy(fn($p) => $p->requirement->category->name ?? 'Uncategorized');

    $categorySummary = $categoryWise->map(function ($group) {
        return [
            'total_budget' => $group->sum('budget')/100000,
            'total_revenue' => $group->sum('revenue')/100000,
            'total_invoice_raised' => $group->sum(fn($p) => $p->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->sum('total_amount'))/100000,
            'total_invoice_raised_tax' => $group->sum(fn($p) => $p->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->where('invoice_type',2)->sum('total_amount'))/100000,
            'total_invoice_raised_proforma' => $group->sum(fn($p) => $p->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->where('invoice_type',1)->sum('total_amount'))/100000,
            'total_invoice_paid' => $group->sum(fn($p) => $p->invoices->sum(fn($i) => $i->payments->sum('amount')))/100000,
            'total_balance' => $group->sum(fn($p) => $p->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->sum('total_amount') - $p->invoices->sum(fn($i) => $i->payments->sum('amount')))/100000,
            'ongoing_count' => $group->where('status', Project::STATUS_ONGOING)->count(),
            'completed_count' => $group->where('status', Project::STATUS_COMPLETED)->count(),
            'archived_count' => $group->where('status', Project::STATUS_ARCHIVED)->count(),
            'initiated_count' => $group->where('status', Project::STATUS_INITIATED)->count(),
            'delayed_count' => $group->where('status', Project::STATUS_ONGOING)->where('end_date', '<', Carbon::today())->count()
        ];
    });


    $investigatorWise = $projects->groupBy(fn($p) => $p->investigator->name ?? 'No Investigator');

$investigatorSummary = $investigatorWise->map(function ($group) {
    return [
        'total_budget' => $group->sum('budget') / 100000,
        'total_revenue' => $group->sum('revenue') / 100000,
        'total_invoice_raised' => $group->sum(fn($p) => $p->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->sum('total_amount')) / 100000,
        'total_invoice_raised_tax' => $group->sum(fn($p) => $p->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->where('invoice_type',2)->sum('total_amount'))/100000,
        'total_invoice_raised_proforma' => $group->sum(fn($p) => $p->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->where('invoice_type',1)->sum('total_amount'))/100000,
        'total_invoice_paid' => $group->sum(fn($p) => $p->invoices->sum(fn($i) => $i->payments->sum('amount'))) / 100000,
        'total_balance' => $group->sum(fn($p) => $p->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->sum('total_amount') - $p->invoices->sum(fn($i) => $i->payments->sum('amount'))) / 100000,
        'ongoing_count' => $group->where('status', Project::STATUS_ONGOING)->count(),
        'completed_count' => $group->where('status', Project::STATUS_COMPLETED)->count(),
        'initiated_count' => $group->where('status', Project::STATUS_INITIATED)->count(),
         'archived_count' => $group->where('status', Project::STATUS_ARCHIVED)->count(),
        'delayed_count' => $group->where('status', Project::STATUS_ONGOING)->where('end_date', '<', Carbon::today())->count()
    ];
});

$investigatorCategoryWise = $projects
    ->groupBy(fn($p) => $p->investigator->name ?? 'No Investigator')
    ->map(function ($group) {
        return $group->groupBy(fn($p) => $p->requirement->category->name ?? 'Uncategorized')
            ->map(function ($catGroup) {
                return [
                    'total_budget' => $catGroup->sum('budget') / 100000,
                    'total_revenue' => $catGroup->sum('revenue') / 100000,
                    'total_invoice_raised' => $catGroup->sum(fn($p) => $p->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->sum('total_amount')) / 100000,
                        'total_invoice_raised_tax' => $catGroup->sum(fn($p) => $p->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->where('invoice_type',2)->sum('total_amount'))/100000,
            'total_invoice_raised_proforma' => $catGroup->sum(fn($p) => $p->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->where('invoice_type',1)->sum('total_amount'))/100000,
                    'total_invoice_paid' => $catGroup->sum(fn($p) => $p->invoices->sum(fn($i) => $i->payments->sum('amount'))) / 100000,
                    'total_balance' => $catGroup->sum(fn($p) => $p->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->sum('total_amount') - $p->invoices->sum(fn($i) => $i->payments->sum('amount'))) / 100000,
                    'ongoing_count' => $catGroup->where('status', Project::STATUS_ONGOING)->count(),
                     'completed_count' => $catGroup->where('status', Project::STATUS_COMPLETED)->count(),
                    'delayed_count' => $catGroup->where('status', Project::STATUS_ONGOING)->where('end_date', '<', Carbon::today())->count(),
                    'initiated_count' => $catGroup->where('status', Project::STATUS_INITIATED)->count(),
                     'archived_count' => $catGroup->where('status', Project::STATUS_ARCHIVED)->count(),
                ];
            });
    })
     // ✅ Sort investigators by total revenue, then project count
    ->sortByDesc(function ($categories, $investigator) {
        $totalRevenue = $categories->sum('total_revenue');
        $projectCount = $categories->sum(fn($c) =>
            ($c['ongoing_count'] ?? 0)
            + ($c['completed_count'] ?? 0)
            + ($c['initiated_count'] ?? 0)
            // + ($c['archived_count'] ?? 0)
        );
        return [$totalRevenue, $projectCount]; // Laravel compares arrays in order
    });

    // Proposal submitted but no client status
    $proposalsPending = Proposal::with('requirement.category')
        ->whereNull('client_status')
        ->get();

    // Initiated stage requirements
    $initiatedRequirements = Requirement::with('category')
        ->where('proposal_status', 0)
        ->get();

    return view('pms.reports.project-status-detailed', [
        'projects' => $projects,
        'categoryWise' => $categoryWise,
         'categorySummary_proposalSubmitted' => $categorySummary_proposalSubmitted,
          'categorySummary_planningStage' => $categorySummary_planningStage,
        'categorySummary' => $categorySummary,
        'proposalsPending' => $proposalsPending,
        'initiatedRequirements' => $initiatedRequirements,
         'liveProjects'      => $liveProjects,
          'ongoingProjects'      => $ongoingProjects,
            'proposalSubmitted' => $proposalSubmitted,
            'planningStage'     => $planningStage,
            'completedProjects' =>$completedProjects,
            'delayedProjects' =>$delayedProjects,
            'archived'=>$archived,
            'investigatorSummary' => $investigatorSummary,
            'investigatorCategoryWise' => $investigatorCategoryWise,
        'pageConfigs'=>$pageConfigs
    ]);
}



    public function financial(Request $request)
    {
      $pageConfigs = ['myLayout' => 'horizontal'];
        $dateRange = $request->input('date_range', 'this_year');

        $query = Project::query();
        $query = $this->applyDateRangeFilter($query, $dateRange);

        $projects = $query->with(['invoices.payments'])->get();

        $financialData = [
            'total_budget' => $projects->sum('budget'),
            'total_revenue' => $projects->sum('revenue'),
            'total_expense' => $projects->sum('estimated_expense'),
            'total_invoiced' => $projects->sum(function($project) {
                return $project->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->sum('total_amount');
            }),
              'total_proforma_invoiced' => $projects->sum(function($project) {
                return $project->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->where('invoice_type',1)->sum('total_amount');
            }),
             'total_tax_invoiced' => $projects->sum(function($project) {
                return $project->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->where('invoice_type',2)->sum('total_amount');
            }),
            'total_paid' => $projects->sum(function($project) {
                return $project->invoices->sum(function($invoice) {
                    return $invoice->payments->sum('amount');
                });
            }),
            'total_pending' => $projects->sum(function($project) {
                return $project->invoices->whereIn('status', [Invoice::STATUS_SENT,Invoice::STATUS_PAID])->sum('total_amount') - $project->invoices->sum(function($invoice) {
                    return $invoice->payments->sum('amount');
                });
            }),
        ];

        $dateRanges = [
            'this_week' => 'This Week',
            'this_month' => 'This Month',
            'this_quarter' => 'This Quarter',
            'this_year' => 'This Year',
            'last_week' => 'Last Week',
            'last_month' => 'Last Month',
            'last_quarter' => 'Last Quarter',
            'last_year' => 'Last Year',
            'custom' => 'Custom Range'
        ];

        return view('pms.reports.financial', compact('financialData', 'dateRanges', 'dateRange'),['pageConfigs'=> $pageConfigs]);
    }

    public function timesheet(Request $request)
    {
      $pageConfigs = ['myLayout' => 'horizontal'];
        $userId = $request->input('user_id', 'all');
        $dateRange = $request->input('date_range', 'this_month');

        $query = Timesheet::query();

        // Apply user filter
        if ($userId !== 'all') {
            $query->where('user_id', $userId);
        }

        // Apply date range filter
        $query = $this->applyDateRangeFilter($query, $dateRange);

        $timesheets = $query->with(['user', 'category', 'project'])->get();

        $groupedTimesheets = $timesheets->groupBy(['user_id', 'category_id']);

        $users = User::whereHas('timesheets')->get();

        $dateRanges = [
            'this_week' => 'This Week',
            'this_month' => 'This Month',
            'this_quarter' => 'This Quarter',
            'this_year' => 'This Year',
            'last_week' => 'Last Week',
            'last_month' => 'Last Month',
            'last_quarter' => 'Last Quarter',
            'last_year' => 'Last Year',
            'custom' => 'Custom Range'
        ];

        return view('pms.reports.timesheet', compact('groupedTimesheets', 'users', 'userId', 'dateRanges', 'dateRange'),['pageConfigs'=> $pageConfigs]);
    }

    public function resourceUtilization(Request $request)
    {
      $pageConfigs = ['myLayout' => 'horizontal'];
        $dateRange = $request->input('date_range', 'this_month');

        $query = Timesheet::query();
        if (!auth()->user()->hasRole('director')) {
          $query->where('user_id', auth()->id());
        }
        $query = $this->applyDateRangeFilter($query, $dateRange);

        $timesheets = $query->with(['user', 'project'])->get();

        // Calculate utilization per user
        $workingDays = $this->getWorkingDaysCount($dateRange);
        $totalAvailableHours = $workingDays * 8; // Assuming 8 hours per day

        $utilizationData = [];

        foreach ($timesheets->groupBy('user_id') as $userId => $userTimesheets) {
            $user = $userTimesheets->first()->user;
            $totalHours = $userTimesheets->sum('hours');
            $utilizationPercentage = ($totalHours / $totalAvailableHours) * 100;

            $utilizationData[] = [
                'user' => $user,
                'total_hours' => $totalHours,
                'utilization_percentage' => $utilizationPercentage,
                // 'projects' => $userTimesheets->groupBy('project_id')->map(function($projectTimesheets) {
                //     return [
                //         'project' => $projectTimesheets->first()->project,
                //         'hours' => $projectTimesheets->sum('hours')
                //     ];
                // })
                'projects' => $userTimesheets
                    ->groupBy(function ($item) {
                        return $item->project_id . '-' . $item->category_id;
                    })
                    ->map(function ($groupedTimesheets) {
                        $first = $groupedTimesheets->first();

                        return [
                            'project' => $first->project,        // May be null
                            'category' => $first->category,      // Always present
                            'hours' => $groupedTimesheets->sum('hours'),
                        ];
                    }),
            ];
        }

        $dateRanges = [
            'this_week' => 'This Week',
            'this_month' => 'This Month',
            'this_quarter' => 'This Quarter',
            'this_year' => 'This Year',
            'last_week' => 'Last Week',
            'last_month' => 'Last Month',
            'last_quarter' => 'Last Quarter',
            'last_year' => 'Last Year',
            'custom' => 'Custom Range'
        ];

        return view('pms.reports.resource-utilization', compact('utilizationData', 'dateRanges', 'dateRange', 'workingDays'),['pageConfigs'=> $pageConfigs]);
    }

    public function export($type, Request $request)
    {
        // Implement export functionality based on $type (excel, pdf, csv)
        // You can use Laravel Excel package for this
        // This is a placeholder implementation

        return redirect()->back()
            ->with('success', 'Export functionality will be implemented soon.');
    }

    private function applyDateRangeFilter($query, $dateRange)
    {
        switch ($dateRange) {
            case 'this_week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                break;
            case 'this_quarter':
                $query->whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()]);
                break;
            case 'this_year':
                $query->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]);
                break;
            case 'last_week':
                $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
                break;
            case 'last_month':
                $query->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()]);
                break;
            case 'last_quarter':
                $query->whereBetween('created_at', [Carbon::now()->subQuarter()->startOfQuarter(), Carbon::now()->subQuarter()->endOfQuarter()]);
                break;
            case 'last_year':
                $query->whereBetween('created_at', [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()]);
                break;
            case 'custom':
                if ($request->has('start_date') && $request->has('end_date')) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($request->start_date)->startOfDay(),
                        Carbon::parse($request->end_date)->endOfDay()
                    ]);
                }
                break;
        }

        return $query;
    }

    private function getWorkingDaysCount($dateRange)
    {
        // This is a simplified version - you might want to account for holidays
        switch ($dateRange) {
            case 'this_week':
            case 'last_week':
                return 5;
            case 'this_month':
            case 'last_month':
                return 20; // Approximate
            case 'this_quarter':
            case 'last_quarter':
                return 60; // Approximate
            case 'this_year':
            case 'last_year':
                return 240; // Approximate
            default:
                return 20; // Default for custom range
        }
    }



    public function projectDetailedReport1(Request $request)
{
    $pageConfigs = ['myLayout' => 'horizontal'];
    $user = Auth::user();

    // Filters
    $status = $request->input('status', 'all');
    $client = $request->input('client', 'all');
    $investigator = $request->input('investigator', 'all');
    $category = $request->input('category', 'all');
    $dateRange = $request->input('date_range', 'this_year');

    $query = Project::with([
        'requirement.client',
        'requirement.category',
        'investigator',
        'milestones.tasks',
        'invoices.payments',
        'expenses',
        'teamMembers.user'
    ]);

    // Role restriction
    if (!$user->hasRole('director') && !$user->hasRole('finance')) {
        $query->where('project_investigator_id', $user->id);
    }

    // Filters
    if ($status !== 'all') {
        $query->where('status', $status);
    }
    if ($client !== 'all') {
        $query->whereHas('requirement.client', function ($q) use ($client) {
            $q->where('id', $client);
        });
    }
    if ($investigator !== 'all') {
        $query->where('project_investigator_id', $investigator);
    }
    if ($category !== 'all') {
        $query->whereHas('requirement.category', function ($q) use ($category) {
            $q->where('id', $category);
        });
    }

    // Date range filter
    $query = $this->applyDateRangeFilter($query, $dateRange);

    $projects = $query->get();

    // Dropdown data
    $statuses = [
        'all' => 'All Statuses',
        0 => 'Initiated',
        1 => 'Ongoing',
        2 => 'Completed',
        3 => 'Archived',
    ];

    $clients = Client::select('id', 'client_name')->get();
    // $investigators = User::role('investigator')->select('id', 'name')->get();
    $investigators = User::whereHas('employee', function($q) {
$q->whereIn('designation', [2, 7, 9]);
    })->get();
    $categories = ProjectCategory::select('id', 'name')->get();

    // Totals
    $totals = [
        'budget' => $projects->sum('budget'),
        'estimated_expense' => $projects->sum('estimated_expense'),
        'actual_expense' => $projects->sum(fn($p) => $p->expenses->sum('total_amount')),
        'invoiced' => $projects->sum(fn($p) => $p->invoices->sum('total_amount')),
        'paid' => $projects->sum(fn($p) => $p->invoices->sum(fn($i) => $i->payments->sum('amount'))),
    ];
    $totals['balance'] = $totals['invoiced'] - $totals['paid'];

    return view('pms.reports.project-status-report', compact(
        'projects', 'statuses', 'clients', 'investigators', 'categories',
        'status', 'client', 'investigator', 'category', 'dateRange', 'totals'
    ), ['pageConfigs' => $pageConfigs]);
}

   public function projectDetailedReport(Request $request)
{

  $pageConfigs = ['myLayout' => 'horizontal'];
//  $investigators = User::whereHas('projects')->orderBy('name')->get();
    $investigators = User::whereHas('employee', function($q) {
$q->whereIn('designation', [2, 7, 9]);
    })->get();

    $clients = Client::orderBy('client_name')->get();



    // Base query with relations
    $query = Project::with([
        'requirement.client',
        'investigator',
        'milestones.tasks',
        'invoices.payments',
        'expenses',
        'teamMembers.user'
    ]);

    // Apply filters
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('investigator_id')) {
        $query->where('project_investigator_id', $request->investigator_id);
    }

    if ($request->filled('client_id')) {
        $query->whereHas('requirement', function ($q) use ($request) {
            $q->where('client_id', $request->client_id);
        });
    }

    $projects = $query->get();

    // Calculate stats
    $projects->map(function ($project) {
        $budget = $project->budget ?? 0;
        $expenses = $project->expenses->sum('total_amount');
        $budgetRemaining = $budget - $expenses;
        $budgetUtilization = $budget > 0 ? ($expenses / $budget) * 100 : 0;

        $totalInvoiced = $project->invoices
            ->whereIn('status', [\App\Models\PMS\Invoice::STATUS_SENT, \App\Models\PMS\Invoice::STATUS_PAID])
            ->sum('total_amount');

        $totalPaid = $project->invoices->sum(function ($invoice) {
            return $invoice->payments->sum('amount');
        });

        $project->calculated = [
            'budget' => $budget,
            'expenses' => $expenses,
            'budget_remaining' => $budgetRemaining,
            'budget_utilization' => round($budgetUtilization, 2),
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'outstanding' => $totalInvoiced - $totalPaid,
        ];

        return $project;
    });

    return view('pms.reports.project-status-report', compact(
        'projects',
        'pageConfigs',
        'investigators',
        'clients',
        'request'
    ));

    // return view('pms.reports.project-status-report', compact('projects', 'pageConfigs'));
}
}