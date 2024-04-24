<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\LeaveRequestDetails;
use App\Models\MissedPunch;
use App\Models\Movement;
use App\Models\Holiday;
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
      $from = date('Y-m-01');

      // Get today's date
      // $to = date('Y-m-d');
      $id= Auth::user()->id;

      $data = AttendanceLog::select('date')->orderBy('date', 'desc')->first();
      $to= $data->date;

      $DurationMinutes = AttendanceLog::whereBetween('AttendanceLogs.date', [$from, $to])->where('AttendanceLogs.user_id',$id)->sum('Duration');
      // $LateBy = AttendanceLog::whereBetween('AttendanceLogs.date', [$from, $to])->where('AttendanceLogs.user_id',$id)->sum('LateBy');
      // $EarlyBy = AttendanceLog::whereBetween('AttendanceLogs.date', [$from, $to])->where('AttendanceLogs.user_id',$id)->sum('EarlyBy');
      $grace=240;

      $durationHours = floor($DurationMinutes / 60) . " hours " . ($DurationMinutes % 60) . " minutes";
      // $LateByHours = floor($LateBy / 60) . " hours " . ($LateBy % 60) . " minutes";
      // $EarlyByHours = floor($EarlyBy / 60) . " hours " . ($EarlyBy % 60) . " minutes";

      function generateDatesWithWeekday($start_date, $end_date) {
        $dates = array();
        $current_date = strtotime($start_date);
        $end_date = strtotime($end_date);

        while ($current_date <= $end_date) {
            $dates[] = array(
                'date' => date('Y-m-d', $current_date),
                'weekday' => date('l', $current_date)
            );
            $current_date = strtotime('+1 day', $current_date);
        }

        return $dates;
    }

    // Example usage:
    $start_date = $from;
    $end_date = $to;
    $date_range_array = generateDatesWithWeekday($start_date, $end_date);
    // print_r($date_range);
    $attendance_data = AttendanceLog::whereBetween('AttendanceLogs.date', [$from, $to])->where('AttendanceLogs.user_id',$id)->orderBy('date')->get();

    $holidays= Holiday::whereBetween('holidays.date', [$from, $to])->get();
    $leaves = LeaveRequestDetails::select('leave_request_details.leave_day_type','leave_request_details.user_id as leave_user_id','leave_request_details.date as leave_date',
    'leave_request_details.status as leave_status','emp.name as leave_action_by_name','leaves.leave_type',)
   ->leftjoin("employees as emp","emp.user_id","=","leave_request_details.action_by")
   ->leftjoin("leaves","leaves.id","=","leave_request_details.leave_type_id")
  ->where('leave_request_details.user_id',$id)
  ->whereBetween('leave_request_details.date', [$from, $to])
  ->orderBy('leave_request_details.user_id','DESC')->get();

  $movements = Movement::select('movements.user_id as mov_user_id','movements.title','movements.type','movements.start_date','movements.start_time','movements.end_date','movements.end_time','movements.status as movement_status',
  'emp_mov.name as movement_action_by_name','movements.remark','movements.location','movements.description')
->leftjoin("employees as emp_mov","emp_mov.user_id","=","movements.action_by")
->where('movements.user_id',$id)
  ->whereBetween('movements.start_date', [$from, $to])
  ->whereBetween('movements.end_date', [$from, $to])
  ->orderBy('movements.user_id','DESC')->get();

  $missedpunches = MissedPunch::select('missed_punches.user_id as miss_user_id','missed_punches.type as miss_type','missed_punches.date as miss_date','missed_punches.checkinTime','missed_punches.checkoutTime','missed_punches.status as miss_status',
  'emp_miss.name as misspunch_action_by_name','missed_punches.description')
->leftjoin("employees as emp_miss","emp_miss.user_id","=","missed_punches.action_by")
->where('missed_punches.user_id',$id)
  ->whereBetween('missed_punches.date', [$from, $to])
  ->orderBy('missed_punches.user_id','DESC')->get();


      return view('content.attendance.attendance',['pageConfigs'=> $pageConfigs],compact('durationHours','from','to','grace','DurationMinutes','attendance_data','date_range_array',
    'holidays','leaves','movements','missedpunches'));
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
      'movements.title','movements.type','movements.start_date','movements.start_time','movements.end_date','movements.end_time','movements.status as movement_status',
    'missed_punches.type as miss_type','missed_punches.date as miss_date','missed_punches.checkinTime','missed_punches.checkoutTime','missed_punches.status as miss_status','emp_mov.name as action_by_name','attendances.date as dates',)
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

->leftjoin("missed_punches",function($join){
  $join->on("missed_punches.user_id","=","attendances.user_id");
  $join->on("missed_punches.date","=","attendances.date");


})
->leftjoin("employees as emp_miss","emp_miss.user_id","=","missed_punches.action_by")


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
