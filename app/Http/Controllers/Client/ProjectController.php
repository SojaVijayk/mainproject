<?php

namespace App\Http\Controllers\Client;

use App\Models\Project;
use App\Models\Client;
use App\Models\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        //  $this->middleware('permission:permission-list|permission-create|permission-edit|permission-delete', ['only' => ['index','store']]);
        //  $this->middleware('permission:permission-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:permission-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
        // $this->middleware('role:Client Management',['only' => ['index','create','store','edit','update','destroy']]);
    }
    public function index()
    {
        //
        $pageConfigs = ['myLayout' => 'horizontal'];
        $projects = Project::with('clients')->withCount('clients')->get();
        $clients = Client::get();
        $leads = Employee::whereIn('designation',[1,2,3])->where('status',1)->get();
        $members = Employee::where('status',1)->get();

        $projectTypes=    $rolePermissions = DB::table("project_types")->where("project_types.status",'1')
        ->select('project_types.id','project_types.type_name')
        ->get();

        if(Auth::user()->user_role == 1) {
            return view('content.clients.projects', compact('projects', 'clients', 'projectTypes', 'leads', 'members'));
        }else{
          return view('content.clients.projects',compact('projects','clients','projectTypes','leads','members'),['pageConfigs'=> $pageConfigs]);
        }
      }
    public function getAllProjects(Request $request)
    {

        $projects = Project::select('id','project_name','description','type','leads','members','created_at',DB::raw("(SELECT  GROUP_CONCAT(type_name) FROM project_types
        WHERE FIND_IN_SET(id, type)) as typeName"))
        // DB::raw("(SELECT  GROUP_CONCAT(name) FROM employees
        // WHERE FIND_IN_SET(user_id, leads)) as projectLeads"),
        // DB::raw("(SELECT  GROUP_CONCAT(name) FROM employees
        // WHERE FIND_IN_SET(user_id, members)) as projectMembers"))
        ->withCount('clients')->with('clients')->withCount('leads')->with('leads')
        ->withCount('members')->with('members')->orderBy('id','DESC')->get();
        return response()->json(['data'=> $projects]);



    }

    public function userProjectList()
    {
      $pageConfigs = ['myLayout' => 'horizontal'];
      $id= Auth::user()->id;
      $employee_projects = Employee::with('lead_projects')->withCount('lead_projects')->with('member_projects')->withCount('member_projects')->where('user_id',$id)->first();

      $projectIds=[];
      foreach($employee_projects->lead_projects as $leadProjects){

        array_push($projectIds, $leadProjects->id);
      }
      foreach($employee_projects->member_projects as $memberProjects){

        array_push($projectIds, $memberProjects->id);
      }
      $ownProjects =Project::where('created_by',$id)->pluck('id');

          array_push($projectIds, $ownProjects);

          $projects = Project::with('clients')->withCount('clients')->get();
        $clients = Client::get();
        $leads = Employee::whereIn('designation',[1,2,3])->where('status',1)->get();
        $members = Employee::where('status',1)->get();

        $projectTypes=    $rolePermissions = DB::table("project_types")->where("project_types.status",'1')
        ->select('project_types.id','project_types.type_name')
        ->get();


      $projects = Project::select('id','project_name','description','type','created_at',DB::raw("(SELECT  GROUP_CONCAT(type_name) FROM project_types
      WHERE FIND_IN_SET(id, type)) as typeName"))
      ->withCount('clients')->with('clients')->withCount('leads')->with('leads')
      ->withCount('members')->with('members')->whereIn('projects.id',$projectIds)->orderBy('id','DESC')->get();

        // return view('content.clients.clients',compact('clients','permissions'));
        return view('content.projects.project-management',compact('projects','clients','projectTypes','leads','members'),['pageConfigs'=> $pageConfigs]);


    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //


        // $project = new Project();
        // $project->name = $request->input('project_name');
        // $project->save();
        // $project->clients()->attach($clients);

        $this->validate($request, [
          'project_name' => 'required|unique:projects,project_name',
          'description' => 'required',
          'type' => 'required',
          'clients' => 'required',
          'leads' => 'required',
          'members' => 'required',

      ]);

      $project = Project::create(['project_name' => $request->input('project_name'),
      'description' => $request->input('description'),
      'type' => $request->input('type'), 'created_by' => Auth::user()->id]);
      $project->clients()->attach($request->input('clients'));
      $project->leads()->attach($request->input('leads'));
      $project->members()->attach($request->input('members'));


      // return redirect()->route('roles.index')
      //                 ->with('success','Role created successfully');
      if ($project) {
        return response()->json('created');
      } else {
        return response()->json(['message' => "Internal Server Error"], 500);

      }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }
    public function editProject($id)
    {
        $project = Project::find($id);
        $clients = Client::get();
        $projectClients = DB::table("clients_projects")->where("clients_projects.project_id",$id)
            ->select('clients_projects.client_id','clients_projects.client_id')
            ->get();

          return response()->json(['project'=> $project,'clients'=>$clients,'projectClients'=>$projectClients]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //

        $this->validate($request, [
          'project_name' => 'required',
          'description' => 'required',
          'type' => 'required',
          'clients' => 'required',
      ]);

        $project = Project::find($id);
        $project->project_name = $request->input('project_name');
        $project->description = $request->input('description');
        $project->type = $request->input('type');

        $project->save();

        $project->clients()->sync($request->input('clients'));
        $project->leads()->sync($request->input('leads'));
        $project->members()->sync($request->input('members'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
