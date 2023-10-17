<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::ADMIN_DASHBOARD;

    /**
     * show login form for admin guard
     *
     * @return void
     */
    public function index()
    {
        $pageConfigs = ['myLayout' => 'blank'];
        return view('content.authentications.auth-login-cover', ['pageConfigs' => $pageConfigs]);
    }


    /**
     * login admin
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        // Validate Login Data
        $request->validate([
            'email' => 'required|max:50',
            'password' => 'required',
        ]);

        // Attempt to login
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {

            // Redirect to dashboard
            session()->flash('success', 'Successully Logged in !');

            if( auth()->user()->user_role==1){
              return redirect()->route('dashboard-admin')->with('success', 'Successfully Logged in!');
            }
            else if(auth()->user()->user_role==2){
              return redirect()->route('dashboard-user')->with('success', 'Successfully Logged in!');;
            }

        }
        else {
        //     // Search using username
        //     if (Auth::attempt(['username' => $request->email, 'password' => $request->password], $request->remember)) {
        //         session()->flash('success', 'Successully Logged in !');
        //         if( auth()->user()->user_role==1){
        //         return redirect()->route('dashboard-admin')->with('success', 'Successfully Logged in!');
        //         }
        //         else if(auth()->user()->user_role==2){
        //           return redirect()->route('dashboard-user')->with('success', 'Successfully Logged in!');;
        //         }
        //     }
        //     // error
            session()->flash('error', 'Invalid email and password');
            return back()->with('error', 'Invalid User Information');;
        }
    }



    /**
     * logout admin guard
     *
     * @return void
     */
    public function logout()
    {
        // Auth::guard('admin')->logout();
        auth()->logout();
        return redirect()->route('login');
    }
    public function menu(){


 $jayParsedAry = [
   "menu" => [
         [
            "name" => "Dashboards",
            "icon" => "menu-icon tf-icons ti ti-smart-home",
            "slug" => "dashboard"
         ],
         [
               "name" => "Users",
               "icon" => "menu-icon tf-icons ti ti-users",
               "slug" => "laravel-example",
               "submenu" => [
                  [
                     "url" => "laravel/user-management",
                     "name" => "Employee Management",
                     "slug" => "laravel-example-user-management"
                  ]
               ]
            ],
          [
              "menuHeader" => "App Configurations"
          ],

         [
              "name" => "Roles & Permissions",
              "icon" => "menu-icon tf-icons ti ti-settings",
              "slug" => "app",
              "submenu" => [
                [
                    "url" => "app/roles",
                    "name" => "Roles",
                    "slug" => "app-roles"
                ],
                [
                      "url" => "app/permission",
                      "name" => "Permission",
                      "slug" => "app-permission"
                    ]
              ]
        ],
         [
            "menuHeader" => "Projects Management"
         ],
         [
            "name" => "Clients",
            "icon" => "menu-icon tf-icons ti ti-users",
            "slug" => "client",
            "submenu" => [
              [
                  "url" => "client/list",
                  "name" => "Client List",
                  "slug" => "client-list"
              ]
            ]
       ],
         [
            "name" => "Projects",
            "icon" => "menu-icon tf-icons ti ti-users",
            "slug" => "client-project",
            "submenu" => [
              [
                  "url" => "projects",
                  "name" => "Project List",
                  "slug" => "client-projects"
              ]
            ]
        ],
]
];


    }
}