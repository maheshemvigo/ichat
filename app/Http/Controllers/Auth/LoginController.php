<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Auth;  // And this

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
   // protected $redirectTo = '/simulationcreate';
    protected function authenticated() {
        if (Auth::check()) {
            return redirect()->route('simulationcreate');
        }else{
            return redirect()->route('welcome');
        }
    }

    public function login(Request $request)
    {
        // Validate the form data using the request
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember_me = $request->has('remember') ? true : false; 

        // Attempt to log the user in
        if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')], $remember_me))
             {
                return $this->authenticated($request, Auth::user());
             }else {
                return back()->with('error','your username and password are wrong.')
                             ->withErrors([
                                  'email' => trans('auth.failed'),
                             ]);
             }

    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    
        $this->middleware('guest')->except('logout');
    }
    

}
