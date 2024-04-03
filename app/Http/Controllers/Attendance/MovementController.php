<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\EmploymentType;
use App\Models\Movement;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

use App\Mail\LeaveRequestMail;
use App\Mail\LeaveRequestActionMail;
use Illuminate\Support\Facades\Mail;
use App\Exports\MovementExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class MovementController extends Controller
{
    public function index()
    {
      $pageConfigs = ['myLayout' => 'horizontal'];

      return view('content.attendance.movement',['pageConfigs'=> $pageConfigs]);
    }

    public function movementList()
    {
      $movements = Movement::all();
      $totalCount = $movements->count();
      $approved = Movement::where('status',1)->get()->count();
      $rejected = Movement::where('status',2)->get()->count();
      $pending = Movement::where('status',0)->get()->count();
      $id= Auth::user()->id;

      $list = Movement::join("employees","employees.user_id","=","movements.user_id")
      ->leftjoin("designations","designations.id","=","employees.designation")
      ->leftjoin("employees as emp","emp.user_id","=","movements.action_by")
      ->select('movements.*',DB::raw("DATE_FORMAT(movements.start_date, '%d-%b-%Y') as formatted_start_date"),DB::raw("DATE_FORMAT(movements.end_date, '%d-%b-%Y') as formatted_end_date"),'employees.name','employees.email','employees.profile_pic','designations.designation','emp.name as action_by_name')->where('movements.user_id',$id)
      ->orderBy('movements.status')->get();
        //  $queries = DB::getQueryLog();
        //   $last_query = end($queries);
        //   dd($queries);
      return response()->json(['data'=> $list]);

    }

    public function store(Request $request)
    {
        //
        $this->validate($request, [
          'title' => 'required',
          'type' => 'required|',
          'start_date' => 'required|',
          'start_time' => 'required|',
          'end_date' => 'required|',
          'end_time' => 'required|',
          'location' => 'required|',


      ]);
      $id= Auth::user()->id;
      $date= date('Y-m-d H:i:s');
      // $from = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('start_date'))));
      // $to = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('end_date'))));

      $var = $request->input('start_date');
      $datef = str_replace('/', '-', $var);
      $from=  date('Y-m-d', strtotime($datef));

      $var2 = $request->input('end_date');
      $datet = str_replace('/', '-', $var2);
      $to=  date('Y-m-d', strtotime($datet));

      $permission = Movement::create(['title' => $request->input('title'),'type' => $request->input('type')
      ,'start_date' => $from,'start_time' => $request->input('start_time'),'end_date' => $to,'end_time' => $request->input('end_time')
      ,'location' => $request->input('location'),'description' => $request->input('description'),'user_id' => $id,'status' => 0,'requested_at' => $date
    ]);

      if ($permission) {
        $mailData = [
          'title' => 'Movement Request',
          'button' => 'Take Action',
          'url' => 'http://localhost:8000/movement/approve-list',
          'body' => Auth::user()->name." created a new movement request for the period of ".$request->input('start_date')." - ".$request->input('start_time')." to ".$request->input('end_date')." - ".$request->input('end_time').".  Please login to your account for take action.",
        ];
        $reporting = Employee::where('employees.user_id',Auth::user()->id)
        ->leftjoin("employees as emp","emp.user_id","=","employees.reporting_officer")
        ->select('emp.email')->first();
        // dd($reporting);
        // Mail::to($reporting->email)->send(new LeaveRequestMail($mailData));

        return response()->json('created');
      } else {
        return response()->json(['message' => "Internal Server Error"], 500);

      }
    }

    public function edit($id)
    {
        //
        $designation = Movement::find($id);

          return response()->json(['designation'=> $designation]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        //
        $this->validate($request, [
          'title' => 'required',
          'type' => 'required|',
          'start_date' => 'required|',
          'start_time' => 'required|',
          'end_date' => 'required|',
          'end_time' => 'required|',
          'location' => 'required|',

      ]);

      $date= date('Y-m-d H:i:s');
      // $from = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('start_date'))));
      // $to = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('end_date'))));

      $var = $request->input('start_date');
      $datef = str_replace('/', '-', $var);
      $from=  date('Y-m-d', strtotime($datef));

      $var2 = $request->input('end_date');
      $datet = str_replace('/', '-', $var2);
      $to=  date('Y-m-d', strtotime($datet));

      $designation = Movement::find($id);


      $designation->title = $request->input('title');
      $designation->type = $request->input('type');
      $designation->start_date = $from;
      $designation->start_time = $request->input('start_time');
      $designation->end_date = $to;
      $designation->end_time = $request->input('end_time');
      $designation->location = $request->input('location');
      $designation->description = $request->input('description');
      $designation->requested_at = $date;
      $designation->save();



      if ($designation) {
        return response()->json('Updated');
      } else {
        return response()->json(['message' => "Internal Server Error"], 500);

      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $movement=Movement::find($id);
          $movement->delete(); //returns true/false
    }


    public function approveList()
    {



      $id= Auth::user()->id;

      $list = Movement::join("employees","employees.user_id","=","movements.user_id")
      ->select('movements.*')->where('employees.reporting_officer',$id);

      $totalCount = $list->count();

      $approved = $list->where('movements.status',1)->count();
      $pending = Movement::join("employees","employees.user_id","=","movements.user_id")
      ->select('movements.*')->where('employees.reporting_officer',$id)->where('movements.status',0)->count();
      $rejected =Movement::join("employees","employees.user_id","=","movements.user_id")
      ->select('movements.*')->where('employees.reporting_officer',$id)->where('movements.status',2)->count();
// echo $pending;exit;



      $pageConfigs = ['myLayout' => 'horizontal'];
      return view('content.attendance.movement-approve-list',compact('totalCount','approved','pending','rejected'),['pageConfigs'=> $pageConfigs]);
    }
    public function requestList()
    {

      $id= Auth::user()->id;

      $list = Movement::join("employees","employees.user_id","=","movements.user_id")
      ->leftjoin("designations","designations.id","=","employees.designation")
      ->leftjoin("employees as emp","emp.user_id","=","movements.action_by")
      ->select('movements.*',DB::raw("DATE_FORMAT(movements.start_date, '%d-%b-%Y') as formatted_start_date"),DB::raw("DATE_FORMAT(movements.end_date, '%d-%b-%Y') as formatted_end_date"),'employees.name','employees.email','employees.profile_pic','designations.designation','emp.name as action_by_name',)->where('employees.reporting_officer',$id)
      ->orderBy('movements.status')->get();
        //  $queries = DB::getQueryLog();
        //   $last_query = end($queries);
        //   dd($queries);
      return response()->json(['data'=> $list]);

    }

    public function action(Request $request,  $id)
    {
        //
        $this->validate($request, [
          'status' => 'required',
          'remark' => 'required',

      ]);

      $date= date('Y-m-d H:i:s');

      $designation = Movement::find($id);
      $designation->status = $request->input('status');
      $designation->remark = $request->input('remark');
      $designation->action_at = $date;
      $designation->action_by = Auth::user()->id;
      $designation->save();



      if ($designation) {

        $mailData = [
          'title' => 'Movement Request Action ',
          'button' => 'View',
          'url' => 'http://localhost:8000/movement',
          'body' => Auth::user()->name." take an action against your Movement request for the period of ".$designation->start_date." - ".$designation->start_time." to ".$designation->end_date." -".$designation->end_time.". Please login to your account for detailed view.",
        ];

        $user =  User::find($designation->user_id);
        // Mail::to($user->email)->send(new LeaveRequestActionMail($mailData));


        return response()->json('Updated');
      } else {
        return response()->json(['message' => "Internal Server Error"], 500);

      }
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

      $list = Movement::join("employees","employees.user_id","=","movements.user_id")
      ->leftjoin("designations","designations.id","=","employees.designation")
      ->leftjoin("employees as emp","emp.user_id","=","movements.action_by")
      ->select('movements.*','employees.name','employees.email','employees.profile_pic','designations.designation','emp.name as action_by_name')
      // ->where('movements.user_id',$id)
      // ->orderBy('movements.status')->get();
      ->whereIn('movements.user_id',$employees)
    ->whereBetween('movements.start_date', [$from, $to])
    ->whereBetween('movements.end_date', [$from, $to])
    ->orderBy('movements.user_id','DESC')->get();



    if($request->input('view_type') == 'html'){
      return response()->json(["list"=>$list]);
    }
    else if($request->input('view_type') == 'pdf'){
      $pdf = PDF::loadView('exports.movement-export-pdf', compact('list'));
      return $pdf->download('movement.pdf');

    }
    else if($request->input('view_type') == 'excel'){

      return Excel::download(new MovementExport($list), 'movement.xlsx');

    }
      // return response()->download(public_path('storage/AttendanceRepot01-09-2023To30-09-2023Bulk.pdf'));
    }


}