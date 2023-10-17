<?php


namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Movement;
use App\Models\Leave;
use App\Models\LeaveRequest;
use App\Models\LeaveRequestDetails;
use App\Models\LeaveAssign;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    //
    public function index()
  {
    $pageConfigs = ['myLayout' => 'horizontal'];
    $leave_types = Leave::orderBy('id','DESC')->get();

    $id= Auth::user()->id;

    $employee_details=Employee::where('user_id',$id)->first();

     $leaves_total_credit = LeaveAssign::select('leave_assigns.id','leave_assigns.leave_type as leave_type_id','leaves.leave_type','total_credit','employment_types.employment_type','leave_assigns.status','leave_assigns.created_at')
     ->leftjoin("leaves","leaves.id","=","leave_assigns.leave_type")
     ->join("employment_types","employment_types.id","=","leave_assigns.employment_type")
     ->where('leave_assigns.employment_type',$employee_details->employment_type)
     ->orderBy('leave_assigns.leave_type','ASC')->get();
     $leaves_total_credit_details=[];
     foreach( $leaves_total_credit as $leave_detail){
       $from = date('2023-04-01');
       $to = date('2024-03-31');

       $availed_leave = LeaveRequestDetails::where('status',1)->where('user_id',$employee_details->user_id)->where('leave_type_id',$leave_detail->leave_type_id)->whereBetween('date', [$from, $to])->sum('leave_duration');
       $pending_leave = LeaveRequestDetails::where('status',0)->where('user_id',$employee_details->user_id)->where('leave_type_id',$leave_detail->leave_type_id)->whereBetween('date', [$from, $to])->sum('leave_duration');

       $leave_balance = [
         "leave_type"=>$leave_detail->leave_type,
         "total_leaves_credit"=>$leave_detail->total_credit,
         "availed_leave"=>$availed_leave,
         "pending_leave"=>$pending_leave,
         "balance_credit"=>($leave_detail->total_credit-$availed_leave)

       ];
       array_push($leaves_total_credit_details,$leave_balance);
     }


     $subscriptionDate = $employee_details->doj;
     $dateArray = (explode("-",$subscriptionDate));

// Convert the subscription date to a Carbon instance
$subscriptionDateTime = Carbon::parse($subscriptionDate);

// Calculate the expiration date by adding one year to the subscription date
$expirationDateTime = $subscriptionDateTime->addYear();

// Format the expiration date as YYYY-MM-DD
$date_end = $expirationDateTime->format("Y-m-d");
$date_start= $dateArray[2].'-'. $dateArray[1].'-'.date("Y");


    return view('content.leave.leave-request',compact('leave_types','leaves_total_credit_details','date_start','date_end'),['pageConfigs'=> $pageConfigs]);
  }

  public function leaveList()
  {

    $id= Auth::user()->id;



    $list = LeaveRequest::with('leaveRequestDetails')->join("employees","employees.user_id","=","leave_requests.user_id")
    ->leftjoin("designations","designations.id","=","employees.designation")
    ->leftjoin("leaves","leaves.id","=","leave_requests.leave_type_id")
    ->leftjoin("employees as emp","emp.user_id","=","leave_requests.action_by")
    ->select('leave_requests.*','leaves.leave_type','employees.name','employees.email','employees.profile_pic','designations.designation','emp.name as action_by_name')->where('leave_requests.user_id',$id)->get();
      //  $queries = DB::getQueryLog();
      //   $last_query = end($queries);

    return response()->json(['data'=> $list]);

  }

  public function store(Request $request)
  {
      //
      $this->validate($request, [
        'leave_type_id' => 'required',
        'from' => 'required|',
        'to' => 'required|',
        'date_list' => 'required|',
        'duration' => 'required|',
        'description' => 'required|',

    ]);
    $id= Auth::user()->id;
    $date= date('Y-m-d H:i:s');
    $from = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('from'))));
    $to = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('to'))));
    $date_list = json_encode($request->input('date_list'));

    $permission = LeaveRequest::create(['leave_type_id' => $request->input('leave_type_id'),
    'duration' => $request->input('duration'),
      'from' => $from,
      'to' => $to,
      'date_list' => $date_list,'description' => $request->input('description'),'user_id' => $id,'status' => 0,'requested_at' => $date
  ]);

    if ($permission) {
      foreach($request->input('date_list') as $data){

         LeaveRequestDetails::create(['leave_type_id' => $permission->leave_type_id,
        'request_id' => $permission->id,
        'leave_day_type' => $data['leave_day_type'],
        'leave_duration' => ($data['leave_day_type'] == 1 ? 1 : 0.5),
          'date' => $data['date'],
         'user_id' => $id,'status' => 0,'requested_at' => $date
      ]);
      }

      return response()->json(  $permission);
    } else {
      return response()->json(['message' => "Internal Server Error"], 500);

    }
  }



  public function approveList()
    {


      $id= Auth::user()->id;

      $list = LeaveRequest::join("employees","employees.user_id","=","leave_requests.user_id")
      ->select('leave_requests.*')->where('employees.reporting_officer',$id);

      $totalCount = $list->count();
      $action_started = $list->where('leave_requests.status',1)->count();
      $pending = $list->where('leave_requests.status',0)->count();
      $completed =$list->where('leave_requests.status',2)->count();



      $pageConfigs = ['myLayout' => 'horizontal'];
      return view('content.leave.leave-approve-list',compact('totalCount','action_started','pending','completed'),['pageConfigs'=> $pageConfigs]);
    }
    public function requestList()
    {


      $id= Auth::user()->id;

      $list = LeaveRequest::with('leaveRequestDetails')->join("employees","employees.user_id","=","leave_requests.user_id")
      ->leftjoin("designations","designations.id","=","employees.designation")
      ->leftjoin("leaves","leaves.id","=","leave_requests.leave_type_id")
      ->leftjoin("employees as emp","emp.user_id","=","leave_requests.action_by")
      ->select('leave_requests.*','leaves.leave_type','employees.name','employees.email','employees.profile_pic','designations.designation','emp.name as action_by_name')->where('employees.reporting_officer',$id)->get();
        //  $queries = DB::getQueryLog();
        //   $last_query = end($queries);
        //   dd($queries);
      return response()->json(['data'=> $list]);

    }

    public function edit($id)
    {
        //
      $list = LeaveRequest::with('leaveRequestDetails')->join("employees","employees.user_id","=","leave_requests.user_id")
    ->leftjoin("designations","designations.id","=","employees.designation")
    ->leftjoin("leaves","leaves.id","=","leave_requests.leave_type_id")
    ->select('leave_requests.*','leaves.leave_type','employees.employment_type','employees.doj','employees.name','employees.email','employees.profile_pic','designations.designation')->where('leave_requests.id',$id)->first();

    $total_leaves_credit = LeaveAssign::where('employment_type', $list->employment_type)->where('leave_type', $list->leave_type_id)->first();
    $from = date('2023-04-01');
    $to = date('2024-03-31');

    $availed_leave = LeaveRequestDetails::where('status',1)->where('user_id',$list->user_id)->where('leave_type_id',$list->leave_type_id)->whereBetween('date', [$from, $to])->sum('leave_duration');
    $pending_leave = LeaveRequestDetails::where('status',0)->where('user_id',$list->user_id)->where('leave_type_id',$list->leave_type_id)->whereBetween('date', [$from, $to])->sum('leave_duration');

    $leave_balance = [
      "total_leaves_credit"=>$total_leaves_credit->total_credit,
      "availed_leave"=>$availed_leave,
      "pending_leave"=>$pending_leave,
      "balance_credit"=>($total_leaves_credit->total_credit-$availed_leave),

    ];

    $subscriptionDate = $list->doj;
    $dateArray = (explode("-",$subscriptionDate));

// Convert the subscription date to a Carbon instance
$subscriptionDateTime = Carbon::parse($subscriptionDate);

// Calculate the expiration date by adding one year to the subscription date
$expirationDateTime = $subscriptionDateTime->addYear();

// Format the expiration date as YYYY-MM-DD
$date_end = $expirationDateTime->format("Y-m-d");
$date_start= $dateArray[2].'-'. $dateArray[1].'-'.date("Y");

          return response()->json(['leave_list'=> $list,"leave_balance"=>$leave_balance,'date_start'=>$date_start,'date_end'=>$date_end]);
    }



    public function action(Request $request,  $id)
    {
        //
        $this->validate($request, [
          'status' => 'required',

      ]);

      $date= date('Y-m-d H:i:s');

      $designation = LeaveRequestDetails::find($id);
      $designation->status = $request->input('status');
      $designation->action_at = $date;
      $designation->action_by = Auth::user()->id;
      $designation->save();


      $count =LeaveRequestDetails::where('status',0)->where('request_id',$designation->request_id)->count();
      $data = LeaveRequest::find($designation->request_id);
      $data->status =   $count > 0 ? 1 : 2;
      $data->action_at = $date;
      $data->action_by = Auth::user()->id;
      $data->save();

      if ($designation) {
        $list = LeaveRequest::with('leaveRequestDetails')->join("employees","employees.user_id","=","leave_requests.user_id")
        ->leftjoin("designations","designations.id","=","employees.designation")
        ->leftjoin("leaves","leaves.id","=","leave_requests.leave_type_id")
        ->select('leave_requests.*','leaves.leave_type','employees.employment_type','employees.name','employees.email','employees.profile_pic','designations.designation')->where('leave_requests.id',$designation->request_id)->first();

        $total_leaves_credit = LeaveAssign::where('employment_type', $list->employment_type)->where('leave_type', $list->leave_type_id)->first();
        $from = date('2023-04-01');
        $to = date('2024-03-31');

        $availed_leave = LeaveRequestDetails::where('status',1)->where('user_id',$list->user_id)->where('leave_type_id',$list->leave_type_id)->whereBetween('date', [$from, $to])->sum('leave_duration');
        $pending_leave = LeaveRequestDetails::where('status',0)->where('user_id',$list->user_id)->where('leave_type_id',$list->leave_type_id)->whereBetween('date', [$from, $to])->sum('leave_duration');

        $leave_balance = [
          "total_leaves_credit"=>$total_leaves_credit->total_credit,
          "availed_leave"=>$availed_leave,
          "pending_leave"=>$pending_leave,
          "balance_credit"=>($total_leaves_credit->total_credit-$availed_leave),

        ];

              return response()->json(['leave_list'=> $list,"leave_balance"=>$leave_balance]);
      } else {
        return response()->json(['message' => "Internal Server Error"], 500);

      }
    }

}
