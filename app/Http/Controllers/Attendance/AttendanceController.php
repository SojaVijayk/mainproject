<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\EmploymentType;
use App\Models\Employee;
use App\Imports\AttendanceImport;
use App\Exports\AttendanceExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
// use Barryvdh\DomPDF\Facade as PDF;
// use Barryvdh\DomPDF\Facade\Pdf as PDF;
use PDF;



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

    public function downloadBulk(Request $request){

      $from = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('fromDate'))));
      $to = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('toDate'))));

      if($request->input('type') == 1){
        $employees = [Auth::user()->id];
      }
      else{
        $employees = $request->input('employeeList');
      }
      $attendance = Attendance::select('attendances.*','employees.empId','employees.profile_pic','employees.email','employees.mobile','employees.name','designations.designation','leave_request_details.leave_day_type',
      'leave_request_details.status as leave_status','emp.name as action_by_name','leaves.leave_type',
      'movements.title','movements.type','movements.start_date','movements.start_time','movements.end_date','movements.end_time','movements.status as movement_status','emp_mov.name as action_by_name')
    ->join("employees","employees.user_id","=","attendances.user_id")


    ->leftjoin("leave_request_details",function($join){
      $join->on("leave_request_details.user_id","=","attendances.user_id")
          ->on("leave_request_details.date","=","attendances.date");
  })
  ->leftjoin("employees as emp","emp.user_id","=","leave_request_details.action_by")
  ->leftjoin("leaves","leaves.id","=","leave_request_details.leave_type_id")

  ->leftjoin("movements",function($join){
    $join->on("movements.user_id","=","attendances.user_id")

        // ->whereBetween('movements.start_date', 'attendances.date')
        // ->whereBetween('movements.end_date', 'attendances.date')
        ->whereBetween('attendances.date', ['movements.start_date','movements.end_date']);

})
->leftjoin("employees as emp_mov","emp_mov.user_id","=","movements.action_by")


    ->leftjoin("designations","designations.id","=","employees.designation")
    ->whereIn('attendances.user_id',$employees)
    ->whereBetween('attendances.date', [$from, $to])
    ->orderBy('attendances.user_id','DESC')->get();

    if($request->input('view_type') == 'html'){
      return response()->json(["list"=>$attendance]);
    }
    else if($request->input('view_type') == 'pdf'){
      $pdf = PDF::loadView('exports.attendance-export-pdf', compact('attendance'));
      return $pdf->download('attendance.pdf');


    }
    else if($request->input('view_type') == 'excel'){

      return Excel::download(new AttendanceExport($attendance), 'Attendance.xlsx');

    }
      // return response()->download(public_path('storage/AttendanceRepot01-09-2023To30-09-2023Bulk.pdf'));
    }

    public function import(Request $request)
    {
        Excel::import(new AttendanceImport,  $request->file('file'));
        // Excel::import(new ImportUser,
        // $request->file('file')->store('files'));

        return response()->json("imported");
    }


}
