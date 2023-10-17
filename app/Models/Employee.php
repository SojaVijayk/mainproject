<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','name','employment_type','designation','mobile','empId','doj','email','reporting_officer'];
    public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function lead_projects()
    {
        return $this->belongsToMany(Project::class, 'projects_leads', 'employee_id','project_id');
    }
    public function member_projects()
    {
        return $this->belongsToMany(Project::class, 'projects_members', 'employee_id','project_id');
    }
}
