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
use App\Models\LeaveRequestDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;


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
    $where = ['id' => $id];

    $users = User::where($where)->first();

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

    $list = LeaveRequest::with('leaveRequestDetails')->join("employees","employees.user_id","=","leave_requests.user_id")
    ->leftjoin("designations","designations.id","=","employees.designation")
    ->leftjoin("leaves","leaves.id","=","leave_requests.leave_type_id")
    ->leftjoin("employees as emp","emp.user_id","=","leave_requests.action_by")
    ->select('leave_requests.*','leaves.leave_type','employees.name','employees.email','employees.profile_pic','designations.designation','emp.name as action_by_name')->where('leave_requests.user_id',$id)->get();
    // $list=
    $j=0;
    $leaves=[];

    foreach( $list as $leave){
      $prop=["calendar"=>'jasar'];
      $obj = [
        'id'=>$j,
        'url'=>'',
        'title'=>'casual',
        'start'=>'2023-10-05',
        'end'=>'2023-10-06',
        'allDay'=>'false',
        'extendedProps'=>$prop

      ];
      array_push($leaves,$obj);

    }

    return response()->json(['events'=> $leaves]);
     }
}