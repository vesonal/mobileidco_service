<?php

namespace App\Http\Controllers\Auth;

use Validator;
use Hash;
use Session;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Admin;
use Illuminate\Support\Facades\Auth;
use DB;

class AdminAuthController extends Controller
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
    protected $redirectTo = '/admin/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function getLogin()
    {
        $user = auth()->guard('admin')->user();
        if(Auth::guard('admin')->check()){
            $user = auth()->guard('admin')->user();
            if ($user!="") {
            return view('admin_dashboard'); 
            }
        }

        return view('auth.admin.login');
    }

    /**
     * Show the application loginprocess.
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
       // if (Hash::check($request->get('password'), DB::table('admins')->where('email', $request->get('email'))->first()->password)) {
       //     // return 'ok';
       //    $user = auth()->guard('admin')->user();
       //      \Session::put('success','You are Login successfully!!');
       //      return redirect()->route('dashboard');
       //  }
       //  else{
       //      return back()->with('error','your username and password are wrong.');
            
       //  }
       
         $attempt=Auth::guard('admin')->attempt($request->only('username','password'));
      // Attempt to log the user in
         //dd(Auth::guard('admin'));
        if (auth()->guard('admin')->attempt(['email' => $request->input('email'), 'password' => $request->input('password')]))
        {
            $user = auth()->guard('admin')->user();
            \Session::put('success','You are Login successfully!!');
            return redirect()->route('clientList');
            
        } else {
            return back()->with('error','your username and password are wrong.');
        }

    }

    /**
     * Show the application logout.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->guard('admin')->logout();
        \Session::flush();
        \Session::put('success','You are logout successfully');        
        return redirect(route('adminLogin'));
    }
}
