<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Models\Designation;
use App\Models\EmploymentType;
use App\Models\LeaveRequest;
use App\Models\Movement;
use App\Models\Attendance;
use App\Models\LeaveRequestDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use DateTime;
use DatePeriod;
use DateInterval;


class EmployeeController extends Controller
{
    //
     /**
   * Redirect to user-management view.
   *
   */
  public function index()
  {
    $users = User::all();
    $userCount = $users->count();
    $verified = User::whereNotNull('email_verified_at')->get()->count();
    $notVerified = User::whereNull('email_verified_at')->get()->count();
    $usersUnique = $users->unique(['email']);
    $userDuplicates = $users->diff($usersUnique)->count();
    $reporting_officers = User::join("employees","employees.user_id","=","users.id")->where('status',1)->get();

    $designations = Designation::where('status',1)->get();
    $employment_types = EmploymentType::orderBy('id','DESC')->get();
    $roles = Role::where('name','!=',"Admin")->get();
    $usertype_roles = DB::table("usertype_role")->where('status',1)->get();

    if(Auth::user()->user_role ==1){
      return view('content.employee.employee-management', [
        'totalUser' => $userCount,
        'verified' => $verified,
        'notVerified' => $notVerified,
        'userDuplicates' => $userDuplicates,
        'designations' => $designations,
        'roles' => $roles,
        'usertype_roles' => $usertype_roles,
        'reporting_officers'=>$reporting_officers,
        'employment_types'=>$employment_types

      ]);
    }
    else{
      $pageConfigs = ['myLayout' => 'horizontal'];
      return view('content.employee.employee-management', [
        'totalUser' => $userCount,
        'verified' => $verified,
        'notVerified' => $notVerified,
        'userDuplicates' => $userDuplicates,
        'designations' => $designations,
        'roles' => $roles,
        'usertype_roles' => $usertype_roles,
        'reporting_officers'=>$reporting_officers,
        'employment_types'=>$employment_types,
        'pageConfigs'=> $pageConfigs

      ]);

    }

  }

  public function employeeList()
  {
    $users = Employee::all();
    $userCount = $users->count();
    $verified = User::whereNotNull('email_verified_at')->get()->count();
    $notVerified = User::whereNull('email_verified_at')->get()->count();
    $usersUnique = $users->unique(['email']);
    $userDuplicates = $users->diff($usersUnique)->count();
    DB::connection()->enableQueryLog();
    $list = User::with("roles")
    ->select('users.*','employees.status','employees.empId','employees.profile_pic','employees.email','employees.mobile','employees.name','designations.designation','usertype_role.usertype_role')
    ->join("employees","employees.user_id","=","users.id")
    ->join("usertype_role","usertype_role.id","=","users.user_role")
    ->leftjoin("designations","designations.id","=","employees.designation")->get();
      //  $queries = DB::getQueryLog();
      //   $last_query = end($queries);
      //   dd($queries);
    return response()->json(['data'=> $list]);

  }
  public function employeeView($id){
    $where = ['users.id' => $id];
    $employee = User::where($where)->with("roles")->join("employees","employees.user_id","=","users.id")
    ->join("usertype_role","usertype_role.id","=","users.user_role")
    ->leftjoin("designations","designations.id","=","employees.designation")->first();
    $employee_projects = Employee::with('lead_projects')->withCount('lead_projects')->with('member_projects')->withCount('member_projects')->where('user_id',$id)->first();

    return view('content.employee.user-employee-view-account',compact('employee','employee_projects'));
  }
  public function profileView(Request $request){
    $id= Auth::user()->id;
    $pageConfigs = ['myLayout' => 'horizontal'];
    $where = ['users.id' => Auth::user()->id];
    $employee = User::where($where)->with("roles")->join("employees","employees.user_id","=","users.id")
    ->join("usertype_role","usertype_role.id","=","users.user_role")
    ->leftjoin("designations","designations.id","=","employees.designation")->first();
    $employee_projects = Employee::with('lead_projects')->withCount('lead_projects')->with('member_projects')->withCount('member_projects')->where('user_id',$id)->first();

    return view('content.employee.user-employee-view-account',compact('employee','employee_projects'),['pageConfigs'=> $pageConfigs]);
  }






  public function employeeAccountView($id){
    $where = ['id' => $id];
    $user = User::where($where)->with("roles")->join("employees","employees.user_id","=","users.id")
    ->join("usertype_role","usertype_role.id","=","users.user_role")
    ->leftjoin("designations","designations.id","=","employees.designation")->first();
    return view('content.employee.user-employee-view-account');

  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */


  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {

        $this->validate($request, [
          'name' => 'required',
          'email' => 'required|email|unique:users,email',
          'mobile' => 'required|numeric|unique:employees,mobile',
          'empId' => 'required|unique:employees,empId',
          // 'password' => 'required|same:confirm-password',
          'doj' => 'required',
          'roles' => 'required',
          'usertype_role' => 'required',
          'designation' => 'required',
          'employment_type'=>'required',
          'reporting_officer'=>'required'
      ]);

        $input = $request->all();
        $password = Hash::make("Abcd@1234");


        $user = User::create(
          ['name'=>$request->name,
          'email'=>$request->email,
          'username'=>$request->empId,
          'password'=>$password,
          'user_role'=>$request->usertype_role,
          ]);
          // foreach($request->input('roles') as $role){
          //   $user->assignRole($role);
          // }
          $user->assignRole($request->input('roles'));

          $doj = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('doj'))));


        $employee = Employee::create(
          ['user_id'=>$user->id,
          'name'=>$request->name,
          'mobile'=>$request->mobile,
          'empId'=>$request->empId,
          'doj'=>$doj,
          'email'=>$request->email,
          'designation'=>$request->designation,
          'employment_type'=>$request->employment_type,
          'reporting_officer'=>$request->reporting_officer

          ,
          ]);


          if($user && $employee ){
            return response()->json('Created');
          } else {
            // user already exist
            return response()->json(['message' => "already exits"], 422);
          }



    // $userID = $request->id;

    // if ($userID) {
    //   // update the value
    //   $users = User::updateOrCreate(
    //     ['id' => $userID],
    //     ['name' => $request->name, 'email' => $request->email]
    //   );

    //   // user updated
    //   return response()->json('Updated');
    // } else {
    //   // create new one if email is unique
    //   $userEmail = User::where('email', $request->email)->first();

    //   if (empty($userEmail)) {
    //     $users = User::updateOrCreate(
    //       ['id' => $userID],
    //       ['name' => $request->name, 'email' => $request->email, 'password' => bcrypt(Str::random(10))]
    //     );

    //     // user created
    //     return response()->json('Created');
    //   } else {
    //     // user already exist
    //     return response()->json(['message' => "already exits"], 422);
    //   }
    // }


  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $where = ['users.id' => $id];

    // $users = User::where($where)->first();
    $users = User::with("roles")
    ->select('users.*','employees.status','employees.empId','employees.profile_pic','employees.email','employees.mobile','employees.name','employees.employment_type','employees.designation as desig_id','employees.doj','employees.reporting_officer','designations.designation','usertype_role.usertype_role')
    ->join("employees","employees.user_id","=","users.id")
    ->join("usertype_role","usertype_role.id","=","users.user_role")
    ->leftjoin("designations","designations.id","=","employees.designation")->where($where)->first();

    return response()->json($users);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $users = User::where('id', $id)->delete();
  }

  public function fetchEvents(Request $request){
    $id= Auth::user()->id;
    $employee_details=Employee::where('user_id',$id)->first();
    $leave_details = LeaveRequestDetails::where('leave_request_details.user_id',$id)
    ->leftjoin("leaves","leaves.id","=","leave_request_details.leave_type_id")
    ->leftjoin("employees as emp","emp.user_id","=","leave_request_details.action_by")
    ->select('leave_request_details.*','leaves.leave_type','emp.name as action_by_name')
    ->get();

    $movement_details = Movement::where('movements.user_id',$id)
    ->leftjoin("employees as emp","emp.user_id","=","movements.action_by")
    ->select('movements.*','emp.name as action_by_name')
    ->get();


    $attendance = Attendance::select('attendances.*','employees.empId','employees.profile_pic','employees.email','employees.mobile','employees.name','designations.designation')
  ->join("employees","employees.user_id","=","attendances.user_id")
  ->leftjoin("designations","designations.id","=","employees.designation")
  ->where('attendances.user_id',$id)
  ->orderBy('attendances.user_id','DESC')->get();


    $j=0;
    $leaves=[];


    foreach( $leave_details as $leave){
      $date_from = strtotime($leave->date);
     $from= date('Y-m-d H:i:s', $date_from);

      $prop=["calendar"=>($leave->status == 1 ? "Approved" :  ($leave->status == 2 ? "Rejected" :  "Requested"))];
      $obj = [
        'id'=>$j,
        'url'=>'',
        'title'=>$leave->leave_type.' '.($leave->status == 1 ? 'Approved (Action By :'.$leave->action_by_name.')' : ($leave->status == 2 ? 'Rejected (Action By :'.$leave->action_by_name.')' : "Pending")),
        'start'=>$from,
        'end'=>$from,
        'allDay'=>($leave->leave_day_type == 1 ? true : false),
        'extendedProps'=>$prop

      ];
      array_push($leaves,$obj);
      $j++;


    }

    foreach( $movement_details as $movement){
      $date_from = strtotime($movement->start_date.' '.$movement->start_time);
      $from= date('Y-m-d H:i:s', $date_from);

      $date_to = strtotime($movement->end_date.' '.$movement->end_time);
      $to= date('Y-m-d H:i:s', $date_to);

      $prop=["calendar"=>($movement->status == 1 ? "Approved" :  ($movement->status == 2 ? "Rejected" :  "Requested"))];
      $obj = [
        'id'=>$j,
        'url'=>'',
        'title'=>$movement->title.' '.($leave->status == 1 ? 'Approved (Action By :'.$leave->action_by_name.')' : ($leave->status == 2 ? 'Rejected (Action By :'.$leave->action_by_name.')' : "Pending")),
        'start'=>$from,
        'end'=>$to,
        'allDay'=>true,
        'extendedProps'=>$prop

      ];
      array_push($leaves,$obj);
      $j++;


    }

    foreach( $attendance as $item){
      $date_from = strtotime($item->date);
     $from= date('Y-m-d H:i:s', $date_from);
      if(($item->in_time == null && $item->out_time == null) || ($item->in_time == '' && $item->out_time == '')){
        $prop=["calendar"=>"Absent"];
      }
      else{
        $prop=["calendar"=>"Present"];
      }

      $obj = [
        'id'=>$j,
        'url'=>'',
        // 'title'=>'IN '.($item->in_time != null && $item->in_time != '' ? $item->in_time : 'No Record' ).' OUT '.$item->out_time,
        'title'=>(($item->in_time != null && $item->in_time != '') && ($item->out_time != null && $item->out_time != '')  ? 'Present (IN '.$item->in_time.' OUT '.$item->in_time.')' : (($item->in_time != null && $item->in_time != '') && ($item->out_time == null || $item->out_time !== '') ? 'IN '.$item->in_time: 'Absent') ),
        'start'=>$from,
        'end'=>$from,
        'allDay'=>true,
        'extendedProps'=>$prop

      ];
      array_push($leaves,$obj);
      $j++;

    }


    return response()->json(['events'=> $leaves]);
     }
}
