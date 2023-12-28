<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['project_name','description','type','total_cost','client_contact_person','initiated_by',
    'contract_staff_strength','field_staff_strength','project_staff_strength','tenure_year','tenure_month','tenure_days','expected_start_date',
    'expected_end_date','additional_support','remarks','created_by'];
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'clients_projects', 'project_id', 'client_id');
    }
    public function leads()
    {
        return $this->belongsToMany(Employee::class, 'projects_leads', 'project_id', 'employee_id');
    }
    public function members()
    {
        return $this->belongsToMany(Employee::class, 'projects_members', 'project_id', 'employee_id');
    }

    public function milestone()
    {
        return $this->hasMany(ProjectMilestone::class,'project_id');
    }
}
