<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ProjectEmployee;
use Illuminate\Support\Facades\DB;

class SalaryManagementController extends Controller
{
    public function index($project_id = null)
    {
        $pageConfigs = ['myLayout' => 'horizontal'];
        
        $employmentTypes = [
            'Apprentice', 'Daily Wages', 'Interns', 'Contract', 
            'Full Time', 'Part Time', 'Freelance', 'Temporary', 'Permanent'
        ];
            
        $years = range(date('Y'), date('Y') - 5);
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        return view('content.projects.salary-management.index', compact('employmentTypes', 'years', 'months', 'pageConfigs', 'project_id'));
    }

    public function selectEmployees(Request $request, $project_id = null)
    {
        $pageConfigs = ['myLayout' => 'horizontal'];
        $month = $request->month;
        $year = $request->year;
        $employmentType = $request->employment_type;

        // Filter active employees by employment type and project_id
        $query = ProjectEmployee::join('service', 'service.p_id', '=', 'project_employee.p_id')
            ->where('service.status', 1)
            ->where('service.employment_type', $employmentType);

        if ($project_id) {
            $query->where('project_employee.project_id', $project_id);
        }

        $employees = $query->select('project_employee.*', 'service.role', 'service.department', 'service.consolidated_pay')
            ->get();

        return view('content.projects.salary-management.select-employees', compact('employees', 'month', 'year', 'employmentType', 'pageConfigs', 'project_id'));
    }

    public function calculation(Request $request, $project_id = null)
    {
        $pageConfigs = ['myLayout' => 'horizontal'];
        $month = $request->month;
        $year = $request->year;
        $employmentType = $request->employment_type;
        $selectedIds = $request->selected_employees ?? [];

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Please select at least one employee.');
        }

        $query = ProjectEmployee::join('service', 'service.p_id', '=', 'project_employee.p_id')
            ->where('service.status', 1)
            ->whereIn('project_employee.p_id', $selectedIds);

        if ($project_id) {
            $query->where('project_employee.project_id', $project_id);
        }

        $employees = $query->leftJoin('designations', 'designations.id', '=', 'project_employee.designation_id')
            ->select('project_employee.*', 'service.role', 'service.department', 'service.consolidated_pay', 'designations.designation')
            ->get();

        return view('content.projects.salary-management.calculation', compact('employees', 'month', 'year', 'employmentType', 'pageConfigs', 'project_id'));
    }

    public function summary(Request $request, $project_id = null)
    {
        $pageConfigs = ['myLayout' => 'horizontal'];
        $month = $request->month;
        $year = $request->year;
        $employmentType = $request->employment_type;
        $p_ids = $request->p_id;
        $workingDays = $request->monthly_working_days;
        $daysWorked = $request->days_worked;
        $baseSalaries = $request->base_salary;
        $totalSalaries = $request->total_salary;

        $summaryData = [];
        foreach ($p_ids as $index => $p_id) {
            $employee = ProjectEmployee::where('p_id', $p_id)->first();
            $summaryData[] = [
                'p_id' => $p_id,
                'name' => $employee->name,
                'working_days' => $workingDays[$index],
                'days_worked' => $daysWorked[$index],
                'base_salary' => $baseSalaries[$index],
                'total_salary' => $totalSalaries[$index],
            ];
        }

        return view('content.projects.salary-management.summary', compact('summaryData', 'month', 'year', 'employmentType', 'pageConfigs', 'project_id'));
    }

    public function store(Request $request, $project_id = null)
    {
        $month = $request->month;
        $year = $request->year;
        $employeeIds = $request->p_id;
        $workingDays = $request->monthly_working_days;
        $daysWorked = $request->days_worked;
        $baseSalaries = $request->base_salary;
        $totalSalaries = $request->total_salary;
        $isFrozen = $request->has('freeze') ? 1 : 0;

        DB::beginTransaction();
        try {
            foreach ($employeeIds as $index => $p_id) {
                \App\Models\Payroll::updateOrCreate(
                    [
                        'p_id' => $p_id,
                        'paymonth' => $month,
                        'year' => $year,
                    ],
                    [
                        'total_working_days' => $workingDays[$index],
                        'days_worked' => $daysWorked[$index],
                        'gross_salary' => $baseSalaries[$index],
                        'net_salary' => $totalSalaries[$index],
                        'lop_days' => $workingDays[$index] - $daysWorked[$index],
                        'is_frozen' => $isFrozen,
                    ]
                );
            }
            DB::commit();
            
            $redirect = $project_id ? route('pms.employees.project-index', $project_id) : route('pms.employees.index');
            $msg = $isFrozen ? 'Payroll frozen successfully ' : 'Payroll processed successfully ';
            
            return redirect($redirect)->with('success', $msg . 'for ' . $month . ' ' . $year);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error processing payroll: ' . $e->getMessage());
        }
    }
}
