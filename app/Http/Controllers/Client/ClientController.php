<?php

namespace App\Http\Controllers\Client;

use App\Models\Client;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        //  $this->middleware('permission:permission-list|permission-create|permission-edit|permission-delete', ['only' => ['index','store']]);
        //  $this->middleware('permission:permission-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:permission-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
        // if(Auth::user()->id!=1){
        //   $this->middleware('role:Client Management');
        // }

    }
    public function index()
    {
      $pageConfigs = ['myLayout' => 'horizontal'];
      $permissions = Permission::get();

        $clients = Client::with('projects')->withCount('projects')->get();
        if(Auth::user()->user_role ==1){
          return view('content.clients.clients',compact('clients','permissions'));
        }else{
          return view('content.clients.clients',compact('clients','permissions'),['pageConfigs'=> $pageConfigs]);
        }



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
        $this->validate($request, [
          'client_name' => 'required|unique:clients,client_name',
          'email' => 'required|unique:clients,email',
          'address' => 'required',
          'phone' => 'required|unique:clients,phone',
          // 'projects' => 'required',
      ]);

      $client = Client::create(['client_name' => $request->input('client_name'),
      'email' => $request->input('email'),
      'address' => $request->input('address'),
      'phone' => $request->input('phone'),
      'created_by' => Auth::user()->id]);
      if ($client) {
        return response()->json('created');
      } else {
        return response()->json(['message' => "Internal Server Error"], 500);

      }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //


    }
    public function editClient($id)
    {
      $client = Client::find($id);

          return response()->json(['client'=> $client]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
          'name' => 'required',

      ]);

      $client = Client::find($id);
      $client->name = $request->input('name');
      $client->save();



      if ($client) {
        return response()->json('Updated');
      } else {
        return response()->json(['message' => "Internal Server Error"], 500);

      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
    }
}