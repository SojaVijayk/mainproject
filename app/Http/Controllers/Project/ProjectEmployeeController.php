<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProjectEmployee;
use App\Models\Designation;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\Service;
use App\Models\Salary;
use App\Models\Deduction;

class ProjectEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id,Request $request)
  {
    $request->session()->forget('project');
    $pageConfigs = ['myLayout' => 'horizontal'];
    $employees = ProjectEmployee::where('project_id',$id)->get();
    $employeeCount = $employees->count();
    $contract = ProjectEmployee::where('user_type',1)->get()->count();
    $consultant = ProjectEmployee::where('user_type',3)->get()->count();
    $dw = ProjectEmployee::where('user_type',2)->get()->count();
    $unique = $employees->unique(['email']);


    $designations = Designation::where('status', 1)->get();
    $roles = Role::where('name', '!=', "Admin")->get();
    $user_types = DB::table("usertype_role")->where('status', 1)->get();
    $project = Project::find($id);

    $request->session()->put('project', $id);
    return view('content.projects.project-employee-management', [
      'totalEmployee' => $employeeCount,
      'contract' => $contract,
      'dw' => $dw,
      'contract' => $contract,
      'consultant'=>$consultant,
      'designations' => $designations,
      'user_types' => $user_types,
      'project_details'=>$project

    ],['pageConfigs'=> $pageConfigs]);
  }

  public function globalIndex(Request $request)
  {
    $pageConfigs = ['myLayout' => 'horizontal'];
    $employees = ProjectEmployee::all();
    $employeeCount = $employees->count();
    
    $designations = Designation::where('status', 1)->get();
    $roles = Role::where('name', '!=', "Admin")->get();
    $user_types = DB::table("usertype_role")->where('status', 1)->get();

    return view('content.projects.project-employee-management', [
      'totalEmployee' => $employeeCount,
      'contract' => 0, // Placeholder or calculate if needed
      'dw' => 0,
      'consultant'=> 0,
      'designations' => $designations,
      'user_types' => $user_types,
      'project_details' => null,
      'is_global' => true
    ],['pageConfigs'=> $pageConfigs]);
  }

  public function globalList(Request $request)
  {
    $list = ProjectEmployee::leftJoin("users", "users.id", "=", "project_employee.user_id")
    ->leftJoin("usertype_role","usertype_role.id","=","users.user_role")
    ->leftJoin("designations","designations.id","=","project_employee.designation_id")
    ->select('project_employee.id','project_employee.name','project_employee.last_name',
      'project_employee.mobile', 'project_employee.email', 'project_employee.status', 
      'project_employee.employee_code', 'project_employee.age', 'project_employee.dob', 'project_employee.date_of_joining', 'project_employee.address',
      'usertype_role.usertype_role as user_type','designations.designation')->get();

    return response()->json(['data'=> $list]);
  }

  public function employeeList(Request $request)
  {

    // DB::connection()->enableQueryLog();
    $id = $request->session()->get('project');
    $list = ProjectEmployee::join("usertype_role","usertype_role.id","=","project_employee.user_type")
    ->leftjoin("designations","designations.id","=","project_employee.designation_id")
    ->leftjoin("gender","gender.id","=","project_employee.gender_id")
    ->select('project_employee.id','project_employee.prefix','project_employee.name','project_employee.last_name',
      'project_employee.profile_pic','project_employee.mobile_pri','project_employee.email_pri','project_employee.status','project_employee.empId',
      'usertype_role.usertype_role as user_type','gender.gender_name','designations.designation')
    ->where('project_id',$id)->get();

    return response()->json(['data'=> $list]);

  }
  // public function employeeView($id){
  //   $where = ['users.id' => $id];
  //   $employee = User::where($where)->with("roles")->join("employees","employees.user_id","=","users.id")
  //   ->join("usertype_role","usertype_role.id","=","users.user_role")
  //   ->leftjoin("designations","designations.id","=","employees.designation")->first();
  //   $employee_projects = Employee::with('lead_projects')->withCount('lead_projects')->with('member_projects')->withCount('member_projects')->where('user_id',$id)->first();

  //   return view('content.employee.user-employee-view-account',compact('employee','employee_projects'));
  // }
  // public function profileView(Request $request){
  //   $id= Auth::user()->id;
  //   $pageConfigs = ['myLayout' => 'horizontal'];
  //   $where = ['users.id' => Auth::user()->id];
  //   $employee = User::where($where)->with("roles")->join("employees","employees.user_id","=","users.id")
  //   ->join("usertype_role","usertype_role.id","=","users.user_role")
  //   ->leftjoin("designations","designations.id","=","employees.designation")->first();
  //   $employee_projects = Employee::with('lead_projects')->withCount('lead_projects')->with('member_projects')->withCount('member_projects')->where('user_id',$id)->first();

  //   return view('content.employee.user-employee-view-account',compact('employee','employee_projects'),['pageConfigs'=> $pageConfigs]);
  // }






  public function globalDetails($id)
  {
    $pageConfigs = ['myLayout' => 'horizontal'];
    $employee = ProjectEmployee::where('id', $id)
      ->with(['service', 'salary', 'deduction', 'designation'])
      ->firstOrFail();

    $designations = Designation::where('status', 1)->get();
    $user_types = DB::table("usertype_role")->where('status', 1)->get();

    return view('content.projects.employee-details', compact('employee', 'pageConfigs', 'designations', 'user_types'));
  }

  public function updateMaster(Request $request, $id)
  {
    $employee = ProjectEmployee::findOrFail($id);
    $data = $request->except(['joining_date', 'designation']); // Exclude mismatched keys
    
    // Map mismatched keys
    if ($request->has('joining_date')) {
        $data['date_of_joining'] = $request->joining_date;
    }
    if ($request->has('designation')) {
        $data['designation_id'] = $request->designation;
    }

    $employee->update($data);
    return response()->json(['success' => true, 'message' => 'Master info updated successfully']);
  }

  public function updateService(Request $request, $p_id)
  {
    $service = Service::updateOrCreate(['p_id' => $p_id], $request->all());
    return response()->json(['success' => true, 'message' => 'Service info updated successfully']);
  }

  public function updateSalary(Request $request, $p_id)
  {
    $data = $request->all();
    $data['gross_salary'] = ($request->basic_pay ?? 0) + ($request->hra ?? 0) + ($request->other_allowance ?? 0);
    $salary = Salary::updateOrCreate(['p_id' => $p_id], $data);
    return response()->json(['success' => true, 'message' => 'Salary info updated successfully']);
  }

  public function updateDeduction(Request $request, $p_id)
  {
    $data = $request->all();
    $data['total_deductions'] = ($request->pf ?? 0) + ($request->esi ?? 0) + ($request->professional_tax ?? 0);
    $deduction = Deduction::updateOrCreate(['p_id' => $p_id], $data);
    return response()->json(['success' => true, 'message' => 'Deduction info updated successfully']);
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
  public function globalStore(Request $request)
  {

        $this->validate($request, [
          'name' => 'required',
          'email' => 'required|email|unique:users,email',
          'mobile' => 'required|numeric',
          'designation' => 'required',
          'age' => 'required',
          'dob' => 'required',
          'joining_date' => 'required',
          'address' => 'required',
      ]);

        $input = $request->all();
        // Auto-generate defaults
        $username = $request->email;
        $password = Hash::make("12345678");
        $usertype_role = 2; // Employee
        $p_id = "EMP-" . time() . rand(100, 999);
        $empId = $p_id; // Same code for now


        $user = User::create(
          ['name'=>$request->name,
          'email'=>$request->email,
          'username'=>$username,
          'password'=>$password,
          'user_role'=>$usertype_role,
          ]);
        
        // Default role assignment if needed
        // $user->assignRole($usertype_role);

        $employee = ProjectEmployee::create(
          ['user_id'=>$user->id,
          'name'=>$request->name,
          'last_name' => '',
          'mobile'=>$request->mobile,
          'email'=>$request->email,
          'designation_id'=>$request->designation,
          'age' => $request->age,
          'dob' => $request->dob,
          'date_of_joining' => $request->joining_date,
          'address' => $request->address,
          'p_id' => $p_id,
          'employee_code' => $empId,
          ]);

          // Store related details
          Service::create([
              'p_id' => $p_id,
              'employment_type' => 'Regular', // Default
              'start_date' => $request->joining_date,
          ]);

          Salary::create([
              'p_id' => $p_id,
              'basic_pay' => 0,
              'hra' => 0,
              'other_allowance' => 0,
              'gross_salary' => 0,
          ]);

          Deduction::create([
              'p_id' => $p_id,
              'pf' => 0,
              'esi' => 0,
              'professional_tax' => 0,
              'total_deductions' => 0,
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
    $employee = ProjectEmployee::where('id', $id)
      ->with(['service', 'salary', 'deduction', 'designation'])
      ->first();

    return response()->json($employee);
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
}