<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = 'employee_payroll';

    protected $fillable = [
        'p_id',
        'paymonth',
        'year',
        'salary_start_date',
        'salary_end_date',
        'basic_pay',
        'da',
        'hra',
        'conveyance_allowance',
        'medical_allowance',
        'special_allowance',
        'other_allowance',
        'bonus',
        'overtime_pay',
        'attendance_bonus',
        'total_working_days',
        'days_worked',
        'lop_days',
        'pf',
        'epf',
        'esi',
        'lic',
        'professional_tax',
        'tds',
        'loan_deduction',
        'gdf',
        'gpf',
        'others',
        'gross_salary',
        'total_deductions',
        'net_salary',
        'is_frozen',
    ];

    public function employee()
    {
        return $this->belongsTo(ProjectEmployee::class, 'p_id', 'p_id');
    }
}
