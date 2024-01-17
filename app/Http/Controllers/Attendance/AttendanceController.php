<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\User;
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

use DB;


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
      $id= Auth::user()->id;
      $employment_types = EmploymentType::orderBy('id','DESC')->get();
      $hr = User::permission('attendance-management')->where('users.id',$id)->count();
      $team = User::permission('team-attendance-management')->where('users.id',$id)->count();

      // print_r($team);exit;
      if($hr > 0 ){
        $employees = Employee::orderBy('id','DESC')->get();
      }
      else if($team > 0){

        $employees = Employee::where('employees.reporting_officer',$id)->orderBy('id','DESC')->get();
      }

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
      // \DB::enableQueryLog();
      $attendance = Attendance::select('attendances.*','employees.empId','employees.profile_pic','employees.email','employees.mobile','employees.name','designations.designation','leave_request_details.leave_day_type',
      'leave_request_details.status as leave_status','emp.name as action_by_name','leaves.leave_type',
      'movements.title','movements.type','movements.start_date','movements.start_time','movements.end_date','movements.end_time','movements.status as movement_status','emp_mov.name as action_by_name','attendances.date as dates')
    ->join("employees","employees.user_id","=","attendances.user_id")


    ->leftjoin("leave_request_details",function($join){
      $join->on("leave_request_details.user_id","=","attendances.user_id")
          ->on("leave_request_details.date","=","attendances.date");
  })
  ->leftjoin("employees as emp","emp.user_id","=","leave_request_details.action_by")
  ->leftjoin("leaves","leaves.id","=","leave_request_details.leave_type_id")

  ->leftjoin("movements",function($join){
    $join->on("movements.user_id","=","attendances.user_id");
    $join->on("movements.start_date",">=","attendances.date");
    $join->on("movements.end_date","<=","attendances.date");


        // ->whereBetween('movements.start_date', [$from,$to])
        // ->whereBetween('movements.end_date', [$from,$to]);
        // ->whereBetween('movements.start_date', ['2023-10-20','2023-10-20'])
        // ->whereBetween('movements.end_date',  ['2023-10-20','2023-10-20']);
        // ->whereBetween('attendances.date', ['movements.start_date','movements.end_date']);

})
->leftjoin("employees as emp_mov","emp_mov.user_id","=","movements.action_by")


    ->leftjoin("designations","designations.id","=","employees.designation")
    ->whereIn('attendances.user_id',$employees)
    ->whereBetween('attendances.date', [$from, $to])
    ->orderBy('attendances.user_id','DESC')->get();
    // dd(\DB::getQueryLog());

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