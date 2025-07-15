<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LeaveRequestDetails;
use App\Models\LeaveDutyAssignment;

class LeaveRequest extends Model
{
    use HasFactory;
    protected $fillable = ['leave_type_id','from','to','duration','date_list','description','status','user_id', 'duty_assigned','requested_at'];

    public function leaveRequestDetails()
    {
        return $this->hasMany(LeaveRequestDetails::class,'request_id');
    }

    public function dutyAssignments()
{
    return $this->hasMany(LeaveDutyAssignment::class);
}

}
