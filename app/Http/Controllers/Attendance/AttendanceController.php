<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\EmploymentType;
use App\Models\Employee;


class AttendanceController extends Controller
{
    //
    public function index()
    {
      $pageConfigs = ['myLayout' => 'horizontal'];
      return view('content.attendance.attendance',['pageConfigs'=> $pageConfigs]);
    }
    public function download(){
      return response()->download(public_path('storage/AttendanceRepot01-09-2023To30-09-2023.pdf'));
    }

    public function attendanceManagement()
    {
      $employment_types = EmploymentType::orderBy('id','DESC')->get();
      $employees = Employee::orderBy('id','DESC')->get();
      $pageConfigs = ['myLayout' => 'horizontal'];
      return view('content.attendance.attendance-management',compact('employment_types','employees'),['pageConfigs'=> $pageConfigs]);
    }

    public function downloadBulk(){
      return response()->download(public_path('storage/AttendanceRepot01-09-2023To30-09-2023Bulk.pdf'));
    }


}