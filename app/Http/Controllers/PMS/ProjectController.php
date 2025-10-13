<?php

namespace App\Http\Controllers\PMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\PMS\ProjectStoreRequest;
use App\Http\Requests\PMS\ProjectUpdateRequest;
use App\Models\PMS\Proposal;
use App\Models\PMS\Project;
use App\Models\PMS\Requirement;
use App\Models\ProjectCategory;
use App\Models\PMS\ExpenseCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class ProjectController extends Controller
{
    public function index()
    {
        $pageConfigs = ['myLayout' => 'horizontal'];
         $user = Auth::user();
        $projects = Project::with(['requirement', 'proposal', 'investigator'])
        ->where(function ($query) use ($user) {
            $query->where('project_investigator_id', $user->id) // Investigator
                  ->orWhereHas('teamMembers', function ($q) use ($user) { // Team Member
                      $q->where('user_id', $user->id);
                  });

            // Role check
            if ($user->hasRole('director')) {
                $query->orWhereRaw('1 = 1'); // Include all projects for director
            }
        })
        ->where('status',0)
            ->latest()
            ->paginate(20);
            $projects_ongoing = Project::with(['requirement', 'proposal', 'investigator'])
        ->where(function ($query) use ($user) {
            $query->where('project_investigator_id', $user->id) // Investigator
                  ->orWhereHas('teamMembers', function ($q) use ($user) { // Team Member
                      $q->where('user_id', $user->id);
                  });

            // Role check
            if ($user->hasRole('director')) {
                $query->orWhereRaw('1 = 1'); // Include all projects for director
            }
        })
        ->where('status',1)
            // ->latest()
            // ->paginate(20);
            ->get();

            $projects_completed = Project::with(['requirement', 'proposal', 'investigator'])
        ->where(function ($query) use ($user) {
            $query->where('project_investigator_id', $user->id) // Investigator
                  ->orWhereHas('teamMembers', function ($q) use ($user) { // Team Member
                      $q->where('user_id', $user->id);
                  });

            // Role check
            if ($user->hasRole('director')) {
                $query->orWhereRaw('1 = 1'); // Include all projects for director
            }
        })
        ->where('status',2)
            ->latest()
            ->paginate(20);

            $projects_archived = Project::with(['requirement', 'proposal', 'investigator'])
        ->where(function ($query) use ($user) {
            $query->where('project_investigator_id', $user->id) // Investigator
                  ->orWhereHas('teamMembers', function ($q) use ($user) { // Team Member
                      $q->where('user_id', $user->id);
                  });

            // Role check
            if ($user->hasRole('director')) {
                $query->orWhereRaw('1 = 1'); // Include all projects for director
            }
        })
        ->where('status',3)
            ->latest()
            ->paginate(20);

        // return view('pms.projects.index', compact('projects'),['pageConfigs'=> $pageConfigs]);
         return view('pms.projects.project-management', compact('projects','projects_ongoing','projects_completed','projects_archived'),['pageConfigs'=> $pageConfigs]);
    }

    // public function create(Proposal $proposal)
    // {
    //     if ($proposal->status != Proposal::STATUS_APPROVED_BY_DIRECTOR ||
    //         !$proposal->client_accepted ||
    //         !$proposal->workOrderDocuments()->exists()) {
    //         return redirect()->route('pms.proposals.show', $proposal->id)
    //             ->with('error', 'Project cannot be created for this proposal. Ensure client has accepted and work order is uploaded.');
    //     }

    //     $categories = ProjectCategory::all();
    //     $faculty = User::whereHas('roles', function($q) {
    //         $q->where('name', 'faculty');
    //     })->get();

    //     return view('pms.projects.create', compact('proposal', 'categories', 'faculty'));
    // }

    public function create(Proposal $proposal)
{
    $pageConfigs = ['myLayout' => 'horizontal'];
    if ($proposal->client_status != 'accepted' || !$proposal->workOrderDocuments()->exists()) {
        return redirect()->route('pms.proposals.show', $proposal->id)
            ->with('error', 'Project cannot be created for this proposal. Ensure client has accepted and work order is uploaded.');
    }

    $categories = ProjectCategory::all();
    $faculty = User::whereHas('roles', function($q) {
        $q->where('name', 'faculty');
    })->get();
     $expenseCategories = ExpenseCategory::all();

    return view('pms.projects.create', compact('proposal', 'categories', 'faculty','expenseCategories'),['pageConfigs'=> $pageConfigs]);
}

    // public function store(ProjectStoreRequest $request, Proposal $proposal)
    // {
    //     if ($proposal->status != Proposal::STATUS_APPROVED_BY_DIRECTOR ||
    //         !$proposal->client_accepted ||
    //         !$proposal->workOrderDocuments()->exists()) {
    //         return redirect()->route('pms.proposals.show', $proposal->id)
    //             ->with('error', 'Project cannot be created for this proposal. Ensure client has accepted and work order is uploaded.');
    //     }

    //     // Generate project code
    //     $clientCode = Str::upper(Str::substr($proposal->requirement->client->client_name, 0, 3));
    //     $categoryCode = $proposal->requirement->category->code;
    //     $year = now()->format('Y');
    //     $serial = Project::whereYear('created_at', $year)->count() + 1;

    //     $projectCode = "{$clientCode}/{$categoryCode}/{$year}/{$serial}";

    //     $data = $request->validated();
    //     $data['project_code'] = $projectCode;
    //     $data['requirement_id'] = $proposal->requirement_id;
    //     $data['proposal_id'] = $proposal->id;
    //     $data['status'] = Project::STATUS_INITIATED;

    //     $project = Project::create($data);

    //     // Add team members
    //     if ($request->has('team_members')) {
    //         foreach ($request->team_members as $memberId) {
    //             $project->teamMembers()->create([
    //                 'user_id' => $memberId,
    //                 'role' => 'member',
    //             ]);
    //         }
    //     }

    //     // Add project investigator as lead
    //     $project->teamMembers()->create([
    //         'user_id' => $request->project_investigator_id,
    //         'role' => 'lead',
    //     ]);

    //     // Copy documents from requirement and proposal to project
    //     $this->copyDocumentsToProject($project, $proposal);

    //     return redirect()->route('pms.projects.show', $project->id)
    //         ->with('success', 'Project created successfully.');
    // }

    public function store(ProjectStoreRequest $request, Proposal $proposal)
{
    if ($proposal->client_status != 'accepted' || !$proposal->workOrderDocuments()->exists()) {
        return redirect()->route('pms.proposals.show', $proposal->id)
            ->with('error', 'Project cannot be created for this proposal. Ensure client has accepted and work order is uploaded.');
    }

    // Generate project code
    // $clientCode = Str::upper(Str::substr($proposal->requirement->client->client_name, 0, 3));
    // $categoryCode = $proposal->requirement->category->code;
    // $year = now()->format('Y');
    // $serial = Project::whereYear('created_at', $year)->count() + 1;

    // $projectCode = "{$clientCode}/{$categoryCode}/{$year}/{$serial}";
    $requirement = $proposal->requirement;
    $clientCode = $requirement->client->code ?? 'CLNT';
    $categoryCode = $requirement->category->code ?? 'CAT';
    $year = $proposal->expected_start_date
        ? $proposal->expected_start_date->format('y')
        : now()->format('y');

        $lastProject = Project::whereHas('requirement.client', fn($q) => $q->where('code', $clientCode))
        ->whereHas('requirement.category', fn($q) => $q->where('code', $categoryCode))
        ->whereYear('start_date', '20' . $year)
        ->orderBy('id', 'desc')
        ->first();

    if ($lastProject && preg_match('/(\d+)$/', $lastProject->project_code, $matches)) {
        $lastSequence = (int) $matches[1];
    } else {
        $lastSequence = 0;
    }

    $newSequence = str_pad($lastSequence + 1, 2, '0', STR_PAD_LEFT);

    // Final project code
    $projectCode = "{$clientCode}/{$categoryCode}/{$year}/{$newSequence}";



    $data = $request->validated();
    $data['project_code'] = $projectCode;
    $data['requirement_id'] = $proposal->requirement_id;
    $data['proposal_id'] = $proposal->id;
    $data['status'] = Project::STATUS_INITIATED;

    $project = Project::create($data);
     $proposal->update(['project_status' =>1]);

    // Add team members
    // if ($request->has('team_members')) {
    //     foreach ($request->team_members as $memberId) {
    //         $project->teamMembers()->create([
    //             'user_id' => $memberId,
    //             'role' => 'member',
    //         ]);
    //     }
    // }

    // Add project investigator as lead
    // $project->teamMembers()->create([
    //     'user_id' => $request->project_investigator_id,
    //     'role' => 'lead',
    // ]);

    // Save project expense components (separate from proposal components)
        foreach ($data['expense_components'] as $component) {
            $project->expenseComponents()->create([
                'expense_category_id' => $component['category_id'],
                'component' => $component['component'],
                'amount' => $component['amount'],
            ]);
        }


    $project->teamMembers()->create([
            'user_id' => $request->project_investigator_id,
            'role' => 'lead',
            'expected_time_investment_minutes' => $request->pi_expected_time * 60,
        ]);
        if ($request->has('team_members_json')) {
        $teamMembers = json_decode($request->input('team_members_json'), true);
        foreach ($teamMembers as $member) {
            $project->teamMembers()->create([
                'user_id' => $member['user_id'],
                'role' => $member['role'],
                'expected_time_investment_minutes' => $member['expected_time'] * 60,
            ]);
        }
      }

    // Copy documents from requirement and proposal to project
    // $this->copyDocumentsToProject($project, $proposal);

    return redirect()->route('pms.projects.show', $project->id)
        ->with('success', 'Project created successfully.');
}

    public function show(Project $project)
    {
        $pageConfigs = ['myLayout' => 'horizontal'];
        $project->load([
            'requirement',
            'proposal',
            'investigator',
            'teamMembers.user',
            'milestones.tasks.assignments.user',
            'documents',
            'invoices.payments'
        ]);

        return view('pms.projects.show', compact('project'),['pageConfigs'=> $pageConfigs]);
    }

    public function edit(Project $project)
    {
        $pageConfigs = ['myLayout' => 'horizontal'];
        if ($project->status != Project::STATUS_INITIATED && $project->status != Project::STATUS_ONGOING) {
            return redirect()->back()
                ->with('error', 'Project cannot be edited in its current status.');
        }

        $project->load(['teamMembers']);
        $teamMemberIds = $project->teamMembers->pluck('user_id')->toArray();
        $teamMembersData = $project->teamMembers->map(function ($member) {
    return [
        'user_id' => $member->user_id,
        'name' => $member->user->name,
        'role' => $member->role,
        'expected_time' => $member->expected_time_investment_minutes
            ? $member->expected_time_investment_minutes / 60
            : 0
    ];
})->values()->toArray();

        $faculty = User::whereHas('roles', function($q) {
            $q->where('name', 'faculty');
        })->get();
        // $staff = User::whereHas('roles', function($q) {
        //     $q->where('name', 'staff');
        // })->get();
         $staff = User::where('active', 1)->get();
          $expenseCategories = ExpenseCategory::whereNotIn('id',[2])->get();

        return view('pms.projects.edit', compact('project', 'faculty', 'staff', 'teamMemberIds','teamMembersData','expenseCategories'),['pageConfigs'=> $pageConfigs]);
    }

    public function update(ProjectUpdateRequest $request, Project $project)
    {
        if ($project->status != Project::STATUS_INITIATED && $project->status != Project::STATUS_ONGOING) {
            return redirect()->back()
                ->with('error', 'Project cannot be edited in its current status.');
        }

        $data = $request->validated();
        $project->update($data);

         // Update project expense components
        $project->expenseComponents()->delete();
        foreach ($data['expense_components'] as $component) {
            $project->expenseComponents()->create([
                'expense_category_id' => $component['category_id'],
                'component' => $component['component'],
                'amount' => $component['amount'],
            ]);
        }



        // Update team members
        // $project->teamMembers()->where('role', 'member')->delete();

        // if ($request->has('team_members')) {
        //     foreach ($request->team_members as $memberId) {
        //         $project->teamMembers()->create([
        //             'user_id' => $memberId,
        //             'role' => 'member',
        //         ]);
        //     }
        // }
        $project->teamMembers()->delete();
        //  $project->teamMembers()->create([
        //     'user_id' => $request->project_investigator_id,
        //     'role' => 'lead',
        //     'expected_time_investment_minutes' => $request->pi_expected_time * 60,
        // ]);

if ($request->has('team_members_json')) {
    $teamMembers = json_decode($request->input('team_members_json'), true);
    foreach ($teamMembers as $member) {
        $project->teamMembers()->create([
            'user_id' => $member['user_id'],
            'role' => $member['role'],
            'expected_time_investment_minutes' => $member['expected_time'] * 60,
        ]);
    }
}

        return redirect()->route('pms.projects.show', $project->id)
            ->with('success', 'Project updated successfully.');
    }

    public function start(Project $project)
    {
        if ($project->status != Project::STATUS_INITIATED) {
            return redirect()->back()
                ->with('error', 'Project cannot be started in its current status.');
        }

        $project->update([
            'status' => Project::STATUS_ONGOING,
            'start_date' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Project started successfully.');
    }

    public function complete(Project $project)
    {
        if ($project->status != Project::STATUS_ONGOING) {
            return redirect()->back()
                ->with('error', 'Project cannot be completed in its current status.');
        }

        $project->update([
            'status' => Project::STATUS_COMPLETED,
            'end_date' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Project marked as completed successfully.');
    }

     public function archive(Project $project)
    {
        if ($project->status != Project::STATUS_COMPLETED) {
            return redirect()->back()
                ->with('error', 'Project cannot be archived in its current status.');
        }

        $project->update([
            'status' => Project::STATUS_ARCHIVED,
            'end_date' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Project marked as archived successfully.');
    }

    public function addDocument(Project $project, Request $request)
    {
        $request->validate([
            'document' => 'required|file|max:10240',
            'type' => 'required|string|max:255',
        ]);

        $file = $request->file('document');
        $path = $file->store("public/projects/{$project->id}/documents");

        $project->documents()->create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'type' => $request->type,
            'size' => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Document uploaded successfully.');
    }

    // private function copyDocumentsToProject(Project $project, Proposal $proposal)
    // {
    //     // Copy requirement documents
    //     foreach ($proposal->requirement->documents as $document) {
    //         $project->documents()->create([
    //             'name' => $document->name,
    //             'path' => $document->path,
    //             'type' => $document->type,
    //             'size' => $document->size,
    //             'uploaded_by' => $document->uploaded_by,
    //         ]);
    //     }

    //     // Copy proposal documents
    //     foreach ($proposal->documents as $document) {
    //         $project->documents()->create([
    //             'name' => $document->name,
    //             'path' => $document->path,
    //             'type' => $document->type,
    //             'size' => $document->size,
    //             'uploaded_by' => $document->uploaded_by,
    //         ]);
    //     }
    // }
    private function copyDocumentsToProject(Project $project, Proposal $proposal)
{
    // Copy requirement documents
    foreach ($proposal->requirement->documents as $document) {
        $project->documents()->create([
            'name' => $document->name,
            'path' => $document->path,
            'type' => $document->type,
            'size' => $document->size,
            'uploaded_by' => $document->uploaded_by,
        ]);
    }

    // Copy proposal documents
    foreach ($proposal->documents as $document) {
        $project->documents()->create([
            'name' => $document->name,
            'path' => $document->path,
            'type' => $document->type,
            'size' => $document->size,
            'uploaded_by' => $document->uploaded_by,
        ]);
    }

    // Copy work order documents
    foreach ($proposal->workOrderDocuments as $document) {
        $project->documents()->create([
            'name' => $document->name,
            'path' => $document->path,
            'type' => 'Work Order',
            'size' => $document->size,
            'uploaded_by' => $document->uploaded_by,
        ]);
    }
}
    public function gantt(Project $project)
    {
        $project->load([
            'milestones.tasks.assignments.user',
            'teamMembers.user'
        ]);

        return view('pms.projects.gantt', compact('project'));
    }

        public function getTeamMembers(Project $project)
    {
        $members = $project->teamMembers()->with('user')->get();
        return response()->json($members);
    }



    //Dashboard
    public function dashboard($id)
    {
        $pageConfigs = ['myLayout' => 'horizontal'];
        $project = Project::with([
            'proposal',
            'milestones.tasks',
            'invoices.payments',
            'expenses',
            'timesheets.user',
            'teamMembers.user'
        ])->findOrFail($id);

        // Budget vs Expenses
        $budget = $project->budget ? $project->budget : 0;
        $expenses = $project->expenses->sum('total_amount');
        $budgetRemaining = $budget - $expenses;
        $budgetUtilization = $budget > 0 ? ($expenses / $budget) * 100 : 0;

        // Revenue vs Invoices
        $totalInvoiced = $project->invoices->sum('amount');
        $totalPaid = $project->invoices->sum(function($invoice) {
            return $invoice->payments->sum('amount');
        });
        $outstanding = $totalInvoiced - $totalPaid;

        // Milestone progress
        $milestoneProgress = $project->milestones->map(function($milestone) {
            return [
                'name' => $milestone->name,
                'progress' => $milestone->getTaskCompletionPercentageAttribute(),
                'status' => $milestone->status_name
            ];
        });

        // Expense by category
        $expenseByCategory = $project->expenses->groupBy('category.name')->map(function($expenses, $category) {
            return $expenses->sum('total_amount');
        });

        // Timesheet data
        $timesheetByUser = $project->timesheets->groupBy('user.name')->map(function($timesheets) {
            return $timesheets->sum('minutes') / 60; // Convert to hours
        });

        // Recent activities
        $recentInvoices = $project->invoices()->latest()->take(5)->get();
        $recentExpenses = $project->expenses()->latest()->take(5)->get();
        $recentTasks = $project->tasks()->latest()->take(5)->get();

        return view('pms.projects.detailed_dashboard', compact(
            'project',
            'budget',
            'expenses',
            'budgetRemaining',
            'budgetUtilization',
            'totalInvoiced',
            'totalPaid',
            'outstanding',
            'milestoneProgress',
            'expenseByCategory',
            'timesheetByUser',
            'recentInvoices',
            'recentExpenses',
            'recentTasks'
        ),['pageConfigs'=> $pageConfigs]);
    }

    public function getProjectFinancialData($id)
    {
        $project = Project::findOrFail($id);

        $financialData = [
            'budget' => $project->proposal ? $project->proposal->budget : 0,
            'expenses' => $project->expenses->sum('total_amount'),
            'invoiced' => $project->invoices->sum('amount'),
            'paid' => $project->invoices->sum(function($invoice) {
                return $invoice->payments->sum('amount');
            })
        ];

        return response()->json($financialData);
    }

    public function getProjectTimelineData($id)
    {
        $project = Project::with(['milestones.tasks'])->findOrFail($id);

        $timelineData = [
            'milestones' => $project->milestones->map(function($milestone) {
                return [
                    'name' => $milestone->name,
                    'progress' => $milestone->getTaskCompletionPercentageAttribute(),
                    'status' => $milestone->status_name,
                    'tasks' => $milestone->tasks->map(function($task) {
                        return [
                            'name' => $task->name,
                            'status' => $task->status_name
                        ];
                    })
                ];
            })
        ];

        return response()->json($timelineData);
    }
}
