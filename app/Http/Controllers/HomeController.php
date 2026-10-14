<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\User;
use App\ChatUser;
use App\AdminUser;
use App\Token;
use Session;
use App\Ipconfig;
use App\Simulation;
use Auth;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // return view('home');
            $user_details = auth()->user();
            $user = $user_details->id;
            return view('simulation.create')->with('user', $user);
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function private()
    {
        return view('private');
    }
    public function start_chat()
    {
        try {

            User::where('id', Auth::user()->id)->where('type', 1)->update(['status' => 0]);
            $simulationid = request()->segment(2);
            Simulation::where('id', $simulationid)->where('status', 0)->update(['edit_status' => 1]);

            if (Session::get('welcome_status') == 1 && Session::get('sess_id') === $simulationid) {
                Session::forget('welcome_status');
                Session::put('welcome_status', 1);
            } else {
                Session::forget('sess_id');
                Session::forget('welcome_status');
                Session::put('welcome_status', 0);
            }

            return view('private')->with('simulationid', $simulationid);
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }


    public function users()
    {
        try {

            //return User::all();
            //checking for user type
            $simulationid = request()->segment(2);
            $login_user = auth()->user()->id;

            if (auth()->user()->type === 0) {
                //return AdminUser::where([['status',0]])->get();
                $simulationid = auth()->user()->simulation_id;
                $data = DB::table("admin_users")->join('users as u', 'u.id', '=', 'admin_users.main_user_id')
                    ->select(
                        "admin_users.*",
                        "u.image",
                        DB::raw("(SELECT count( messages.id) FROM  messages
                WHERE messages.chat_user_identification_id = admin_users.id and messages.seen_status =0 and messages.receiver_id=$login_user ) as message_count")
                    )
                    ->where('admin_users.simulation_id', $simulationid)
                    ->get();

                $getusername = User::select('username')->where('id', $login_user)->first();
                if ($getusername) {
                    $val['username'] = str_replace(' ', '-', ucwords(strtolower($getusername->username)));

                }
                $val['welcome_status'] = Token::select('welcome_status')->where('user_id', $login_user)->where('simulation_id', $simulationid)->first();
                $dataip['ipaddress'] = Ipconfig::join('simulations as s', 'ip_config.created_by', '=', 's.created_by')
                    ->select('ip_config.ip_address')
                    ->where('s.id', $simulationid)
                    ->get();

                if (isset($dataip['ipaddress'][0]->ip_address) && !empty($dataip['ipaddress'][0]->ip_address)) {
                    $val['ipaddress'] = $dataip['ipaddress'][0]->ip_address;
                } else {
                    $val['ipaddress'] = '';
                }
            } else {
                $data = DB::table("chat_users")
                    ->join('users', 'users.id', '=', 'chat_users.main_user_id')
                    ->select("chat_users.*", "users.color_code","users.name as userfirstname", DB::raw("(SELECT count( messages.id) FROM  messages WHERE messages.admin_chat_id = chat_users.id and messages.seen_status =0 and messages.receiver_id=$login_user ) as message_count"))
                    ->where('chat_users.simulation_id', $simulationid)
                    ->orderBy('users.id')
                    ->orderBy('chat_users.id')
                    ->get();
                $getusername = auth()->user()->name;
                if ($getusername) {
                     $val['username'] = str_replace(' ', '-', ucwords(strtolower(auth()->user()->name)));
                }
                $dataip['ipaddress'] = Ipconfig::select('ip_address')
                    ->where('created_by', $login_user)
                    ->where('status', 0)
                    ->get();
                if (isset($dataip['ipaddress'][0]->ip_address) && !empty($dataip['ipaddress'][0]->ip_address)) {
                    $val['ipaddress'] = $dataip['ipaddress'][0]->ip_address;
                } else {
                    $val['ipaddress'] = '';
                }
                if (Session::get('welcome_status') == 1) {
                    $sess = Session::get('welcome_status');
                    //Session::put('welcome_status', 1);   
                } else {
                    Session::put('welcome_status', 0);
                    $sess = Session::get('welcome_status');
                }
                Session::put('sess_id',  $simulationid);
                $val['welcome_status'] = $sess;
            }

            $val['status'] = auth()->user()->status;
            $val['exit_user_id'] = auth()->user()->id;
            $val['var'] = $data;
            $val['user_type'] = auth()->user()->type;
            return response()->json($val);
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function logout()
    {
        try {
            \Session::flush();

            return redirect()->intended('/');
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function document(){
        try{
            $path = 'document';
            return view('document')->with('path', $path);
        }catch(Exception $e){
            report($e);
            return false;
        }
    }
}
