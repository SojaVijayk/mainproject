<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\EmploymentType;
use App\Models\Movement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;

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
      ->select('movements.*','employees.name','employees.email','employees.profile_pic','designations.designation','emp.name as action_by_name')->where('movements.user_id',$id)->get();
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
          'description' => 'required|',

      ]);
      $id= Auth::user()->id;
      $date= date('Y-m-d H:i:s');
      $from = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('start_date'))));
      $to = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('end_date'))));

      $permission = Movement::create(['title' => $request->input('title'),'type' => $request->input('type')
      ,'start_date' => $from,'start_time' => $request->input('start_time'),'end_date' => $to,'end_time' => $request->input('end_time')
      ,'location' => $request->input('location'),'description' => $request->input('description'),'user_id' => $id,'status' => 0,'requested_at' => $date
    ]);

      if ($permission) {
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
          'description' => 'required|',

      ]);

      $date= date('Y-m-d H:i:s');
      $from = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('start_date'))));
      $to = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('end_date'))));

      $designation = Movement::find($id);


      $designation->title = $request->input('title');
      $designation->type = $request->input('type');
      $designation->start_date = $from;
      $designation->start_time = $request->input('start_time');
      $designation->end_date = $request->input('end_date');
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

      $movements = Movement::all();
      $totalCount = $movements->count();
      $approved = Movement::where('status',1)->get()->count();
      $rejected = Movement::where('status',2)->get()->count();
      $pending = Movement::where('status',0)->get()->count();
      $id= Auth::user()->id;
      $pageConfigs = ['myLayout' => 'horizontal'];
      return view('content.attendance.movement-approve-list',['pageConfigs'=> $pageConfigs]);
    }
    public function requestList()
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
      ->select('movements.*','employees.name','employees.email','employees.profile_pic','designations.designation','emp.name as action_by_name',)->where('employees.reporting_officer',$id)->get();
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

      ]);

      $date= date('Y-m-d H:i:s');

      $designation = Movement::find($id);
      $designation->status = $request->input('status');
      $designation->action_at = $date;
      $designation->action_by = Auth::user()->id;
      $designation->save();



      if ($designation) {
        return response()->json('Updated');
      } else {
        return response()->json(['message' => "Internal Server Error"], 500);

      }
    }

}