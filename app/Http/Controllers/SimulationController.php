<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use App\Simulation;
use App\Events\MessageDelivered;
use App\Events\BroadcastMessageSent;
use Illuminate\Validation\Rule;
use App\User;
use App\Token;
use App\ChatUser;
use App\AdminUser;
use App\Message;
use App\Events\ExitSimulation;
use Auth;
use Session;
use Hash;
use DB;
use Redirect;
use App\Favourite;
use App\Upload;
use App\Ipconfig;
use App\Links;
use App\Services\SimulationReportStorageService;
use App\Services\PostHogAnalyticsService;
use App\Jobs\GenerateSimulationHistoryReportJob;

class SimulationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['loginuser', 'loginauth']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if (Auth::user()->type == 1) {
                $user_details = auth()->user();
                $user = $user_details->id;

                $images = Upload::where([['type', 1], ['status', 0]])->orderBy('id', 'DESC')->get();
                $documents = Upload::where([['type', 2], ['status', 0]])->orderBy('id', 'DESC')->get();
                $videos = Upload::where([['type', 0], ['status', 0]])->orderBy('id', 'DESC')->get();
                $links = Links::where('status', 0)->orderBy('id', 'DESC')->get();

                $ipaddress = Ipconfig::select('ip_address')->where('status', 0)->where('created_by', '=', Auth::user()->id)->first();
                $path = 'create';
                return view('simulation.create', compact('images', 'documents', 'videos', 'links'))->with('user', $user)->with('ipaddress', $ipaddress)->with('path', $path);
            } else {
                return redirect('/login');
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function simulationnamecheck(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'simulation_name' => ['required', Rule::unique('simulations')->ignore($request->id)],
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'fail']);
            } else {
                return response()->json(['message' => 'success']);
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function create()
    {
        try {
            if (Auth::user()->type == 1) {
                $path = 'list';
                return view('simulation.simulation_list')->with('path', $path);
            } else {
                return redirect('/login');
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function get_simulation()
    {
        try {
            if (Auth::user()->type == 1) {
                $simulations = Simulation::with('tokens_getter')
                    ->where([['created_by', Auth::user()->id], ['simulations.status', 0]])
                    ->orderBy('id', 'DESC')
                    ->get();
                return response()->json($simulations);
            } else {
                return redirect('/login');
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $simulationForPostHog = null;
        $trackSimulationCreated = false;
        $participantCount = 0;

        try {
            if (Auth::user()->type == 1) {

                User::where('id', Auth::user()->id)->where('type', 1)->update(['status' => 0]);

                $simulation = new Simulation;
                $simulation->simulation_name = $request->simulation_name;
                $simulation->url = '';
                $simulation->status = 0;
                $simulation->created_by = Auth::user()->id;
                $simulation->updated_by = Auth::user()->id;
                $simulation->created_at = date('Y-m-d H:i:s');
                $simulation->updated_at = date('Y-m-d H:i:s');
                $simulation->save();
                $simulationForPostHog = $simulation;
                $trackSimulationCreated = (bool) $simulation->id;

                if ($simulation->id) {
                    $my_array1 = $request->Simulationion_user_name;
                    $my_array2 = $request->mainuser;
                    $my_array3 = $request->mainuser_color;
                    $my_array4 = $request->Simulationion_role;
                    $my_array5 = $request->docid;
                    $my_array6 = $request->linkid;
                    $insertdoc_arr = array();

                    if (isset($my_array1) && is_array($my_array1)) {
                        $participantCount = count($my_array1);
                    }

                    // $main_user = count($my_array4);
                    // $t_user = count($my_array2);

                    $user_color_combination = array_combine($my_array2, $my_array3);
                    // $arr = array($user_color_combination);
                    if (isset($my_array1) && !empty($my_array1)) {
                        foreach ($my_array1  as $key => $value) {
                            $user_obj = new User;
                            if (isset($request->profilepicname[$key]) && $request->profilepicname[$key] != '' && !empty($request->profilepicname[$key])) {
                                $user_obj->image = $request->profilepicname[$key];
                            } else {
                                if ($request->hasFile('profilepic')) {
                                    //dd($request->profilepic);
                                    $getimageName = time() . '_' . $value . '.' . $request->profilepic[$key]->getClientOriginalExtension();
                                    $request->profilepic[$key]->move(public_path('uploads'), $getimageName);

                                    $user_obj->image = $getimageName;
                                }
                            }
                            $user_obj->simulation_id = $simulation->id;
                            $user_obj->name = $value;
                            $user_obj->color_code = '';
                            $user_obj->email = '';
                            $user_obj->password = \Hash::make('12345678');
                            $user_obj->type = 0;
                            $user_obj->remember_token = '';
                            $user_obj->created_at = date('Y-m-d H:i:s');
                            $user_obj->updated_at = date('Y-m-d H:i:s');
                            $user_obj->save();

                            $admin_user_obj = new AdminUser;
                            $admin_user_obj->simulation_id = $simulation->id;
                            $admin_user_obj->user_id =  Auth::user()->id;;
                            $admin_user_obj->main_user_id =  $user_obj->id;
                            $admin_user_obj->name = $value;
                            $admin_user_obj->role = $my_array4[$key];
                            $admin_user_obj->status = 0;
                            $admin_user_obj->created_at = date('Y-m-d H:i:s');
                            $admin_user_obj->updated_at = date('Y-m-d H:i:s');
                            $admin_user_obj->save();
                        }
                    } else {
                        return Redirect::back()->with('msg', 'The Message');
                    }
                    if (isset($user_color_combination) && (!empty($user_color_combination))) {

                        foreach ($user_color_combination as $key => $value) {
                            $user_obj = new User;
                            $user_obj->simulation_id = $simulation->id;
                            $user_obj->name = $key;
                            $user_obj->color_code = $value;
                            $user_obj->email = '';
                            $user_obj->password = \Hash::make('12345678');
                            $user_obj->type = 0;
                            $user_obj->remember_token = '';
                            $user_obj->created_at = date('Y-m-d H:i:s');
                            $user_obj->updated_at = date('Y-m-d H:i:s');
                            $user_obj->status = 0;
                            $user_obj->save();
                        }

                        $userdata = User::where([['simulation_id', '=', $simulation->id], ['type', '=', 0], ['color_code', '!=', '']])->get();
                        $adminuserdetails = AdminUser::where([['simulation_id', '=', $simulation->id]])->get();
                        foreach ($adminuserdetails as $key => $val) {
                            foreach ($userdata as $key => $value) {
                                $chatuser = new ChatUser;
                                $chatuser->name = $value->name . '-' . $val->name . '-' . $val->role;
                                $chatuser->simulation_id = $simulation->id;
                                $chatuser->user_id = $value->id;
                                $chatuser->main_user_id = $value->id;
                                $chatuser->admin_user_id = $val->id;
                                $chatuser->save();
                            }
                        }
                    }
                    if (isset($my_array5) && !empty($my_array5)) {

                        foreach ($my_array5 as $key => $value) {
                            $doc_type = Upload::select('type', 'ext')->where('id', '=', $value)->first();
                            $doc['simulation_id'] = $simulation->id;
                            $doc['upload_file_id'] = $value;
                            $doc['type'] = $doc_type->type;
                            $doc['status'] = 0;
                            $doc['ext'] = $doc_type->ext;
                            $doc['created_by'] = Auth::user()->id;
                            $doc['updated_by'] = Auth::user()->id;
                            $doc['created_at'] = date('Y-m-d H:i:s');
                            $doc['updated_at'] = date('Y-m-d H:i:s');
                            $insertdoc_arr[] = (array) $doc;
                        }
                        DB::table('favourite_images')->insert($insertdoc_arr);
                    }
                    if (isset($my_array6) && !empty($my_array6)) {
                        foreach ($my_array6 as $key => $value) {
                            $link['simulation_id'] = $simulation->id;
                            $link['upload_file_id'] = $value;
                            $link['type'] = 3;
                            $link['status'] = 0;
                            $link['ext'] = 'link';
                            $link['created_by'] = Auth::user()->id;
                            $link['updated_by'] = Auth::user()->id;
                            $link['created_at'] = date('Y-m-d H:i:s');
                            $link['updated_at'] = date('Y-m-d H:i:s');
                            $insertlink_arr[] = (array) $link;
                        }
                        DB::table('favourite_images')->insert($insertlink_arr);
                    }
                }

                return redirect('/simulationlist')->with('success', 'Simulation added successfully');
            } else {
                return redirect('/login');
            }
        } catch (Exception $e) {
            report($e);

            return false;
        } finally {
            if ($trackSimulationCreated && $simulationForPostHog !== null) {
                app(PostHogAnalyticsService::class)->captureSimulationCreated($simulationForPostHog, [
                    'participant_count' => $participantCount,
                    'source' => 'web',
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Simulation  $simulation
     * @return \Illuminate\Http\Response
     */
    public function show(Simulation $simulation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Simulation  $simulation
     * @return \Illuminate\Http\Response
     */
    public function edit(Simulation $simulation)
    {
        try {
            if (Auth::user()->type == 1) {
                $simulationid = request()->segment(2);


                $simulation_name = Simulation::select('simulation_name')
                    ->where('id', $simulationid)
                    ->first();

                $simulation_admin_user_details = AdminUser::where('admin_users.simulation_id', $simulationid)
                    ->leftJoin('users as u', 'u.id', '=', 'admin_users.main_user_id')
                    ->select('u.image', 'admin_users.name', 'admin_users.role')
                    ->get();

                $userterminal_details = User::select('name', 'color_code')
                    ->where('simulation_id', $simulationid)
                    ->where('color_code', '!=', '')
                    ->get();

                $user_details = auth()->user();

                $user = '1';

                $images = Upload::where([['type', 1], ['status', 0]])->orderBy('id', 'DESC')->get();
                $documents = Upload::where([['type', 2], ['status', 0]])->orderBy('id', 'DESC')->get();
                $videos = Upload::where([['type', 0], ['status', 0]])->orderBy('id', 'DESC')->get();
                $links = Links::where('status', 0)->get();

                $ipaddress = Ipconfig::select('ip_address')->where('status', 0)->where('created_by', '=', Auth::user()->id)->first();

                //favourite_docs

                $favourite_video = Favourite::join('upload_files as uf', 'favourite_images.upload_file_id', '=', 'uf.id')->select('favourite_images.upload_file_id', 'favourite_images.type', 'uf.path_name', 'uf.name', 'uf.type')->where('favourite_images.type', 0)->where('favourite_images.simulation_id', $simulationid)->get();
                $favourite_image = Favourite::join('upload_files as uf', 'favourite_images.upload_file_id', '=', 'uf.id')->select('favourite_images.upload_file_id', 'favourite_images.type', 'uf.path_name', 'uf.name', 'uf.type')->where('favourite_images.type', 1)->where('favourite_images.simulation_id', $simulationid)->get();
                $favourite_doc = Favourite::join('upload_files as uf', 'favourite_images.upload_file_id', '=', 'uf.id')->select('favourite_images.upload_file_id', 'favourite_images.type', 'uf.path_name', 'uf.name', 'uf.type')->where('favourite_images.type', 2)->where('favourite_images.simulation_id', $simulationid)->get();

                $favourite_link = Favourite::join('links as l', 'favourite_images.upload_file_id', '=', 'l.id')->select('favourite_images.upload_file_id', 'favourite_images.type', 'l.name', 'l.id')->where('favourite_images.type', 3)->where('favourite_images.simulation_id', $simulationid)->get();

                $path = 'update';
                return view('simulation.update', compact('images', 'documents', 'videos', 'links'))->with(['path' => $path, 'favourite_video' => $favourite_video, 'favourite_image' => $favourite_image, 'favourite_doc' => $favourite_doc, 'favourite_link' => $favourite_link, 'user' => $user, 'simulation_name' => $simulation_name, 'simulation_id' => $simulationid, 'admin_user_details' => $simulation_admin_user_details, 'userterminal_details' => $userterminal_details, 'ipaddress' => $ipaddress]);
            } else {
                return redirect('/login');
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Simulation  $simulation
     * @return \Illuminate\Http\Response
     */

    public function updatesimulationnamecheck(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'simulation_name' => ['required', Rule::unique('simulations')->ignore($request->id)],
            ]);

            $this->validate($request, []);
            if ($validator->fails()) {
                return response()->json(['message' => 'fail']);
            } else {
                return response()->json(['message' => 'success']);
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }
    public function update(Request $request, Simulation $simulation)
    {
        try {
            if (Auth::user()->type == 1) {
                $my_array1 = array();
                $my_array2 = array();
                $my_array3 = array();
                $my_array4 = array();
                $simulationdeleted = Simulation::where('id', $request->simulation_id)->delete();

                if ($simulationdeleted) {
                    $simulation = new Simulation;
                    $simulation->id = $request->simulation_id;
                    $simulation->simulation_name = $request->simulation_name;
                    $simulation->url = '';
                    $simulation->status = 0;
                    $simulation->created_by = Auth::user()->id;
                    $simulation->updated_by = Auth::user()->id;
                    $simulation->created_at = date('Y-m-d H:i:s');
                    $simulation->updated_at = date('Y-m-d H:i:s');
                    $simulation->save();
                }

                if ($simulation->id) {
                    $deleteuser = User::where('simulation_id', $request->simulation_id)->delete();
                    $deleteadminuser = AdminUser::where('simulation_id', $request->simulation_id)->delete();
                    $deletechatuser = ChatUser::where('simulation_id', $request->simulation_id)->delete();
                    $upadtesimulationtable = Simulation::where('id', $request->simulation_id)->update(array('url' => ''));
                    $deletechatuser = Token::where('simulation_id', $request->simulation_id)->delete();
                    $del_fevorites = Favourite::where('simulation_id', $request->simulation_id)->delete();

                    $my_array1 = $request->Simulationion_user_name;
                    $my_array2 = $request->mainuser;
                    $my_array3 = $request->mainuser_color;
                    $my_array4 = $request->Simulationion_role;
                    $my_array5 = $request->docid;
                    $my_array6 = $request->linkid;
                    $main_user = count($my_array4);
                    $t_user = count($my_array2);
                    $user_color_combination = array_combine($my_array2, $my_array3);
                    $arr = array($user_color_combination);

                    if (isset($my_array1) && !empty($my_array1)) {
                        $i = 0;
                        // $j = 0;
                        foreach ($my_array1  as $key => $value) {
                            $user_obj = new User;
                            if (isset($request->profilepicname[$key]) && $request->profilepicname[$key] != '' && !empty($request->profilepicname[$key])) {
                                $user_obj->image = $request->profilepicname[$key];
                                $i++;
                            } else {
                                if ($request->hasFile('profilepic')) {
                                    //   print_r($key);
                                    //   print_r($request->profilepic[$key]);

                                    $getimageName = time() . '_' . $value . '.' . $request->profilepic[$key]->getClientOriginalExtension();

                                    $request->profilepic[$key]->move(public_path('uploads'), $getimageName);

                                    $user_obj->image = $getimageName;
                                    //  dd($user_obj);
                                    //$j++;
                                    // dd($j);
                                }
                            }
                            $user_obj->simulation_id = $simulation->id;
                            $user_obj->name = $value;
                            $user_obj->color_code = '';
                            $user_obj->email = '';
                            $user_obj->password = \Hash::make('12345678');
                            $user_obj->type = 0;
                            $user_obj->remember_token = '';
                            $user_obj->created_at = date('Y-m-d H:i:s');
                            $user_obj->updated_at = date('Y-m-d H:i:s');
                            $newusersave = $user_obj->save();

                            $admin_user_obj = new AdminUser;
                            $admin_user_obj->simulation_id = $simulation->id;
                            $admin_user_obj->user_id =  Auth::user()->id;;
                            $admin_user_obj->main_user_id =  $user_obj->id;
                            $admin_user_obj->name = $value;
                            $admin_user_obj->role = $my_array4[$key];
                            $admin_user_obj->status = 0;
                            $admin_user_obj->created_at = date('Y-m-d H:i:s');
                            $admin_user_obj->updated_at = date('Y-m-d H:i:s');
                            $adminusersave = $admin_user_obj->save();
                        }
                    } else {
                        return Redirect::back()->with('msg', 'The Message');
                    }
                    if (isset($user_color_combination) && (!empty($user_color_combination))) {

                        foreach ($user_color_combination as $key => $value) {
                            $user_obj = new User;
                            $user_obj->simulation_id = $simulation->id;
                            $user_obj->name = $key;
                            $user_obj->color_code = $value;
                            $user_obj->email = '';
                            $user_obj->password = \Hash::make('12345678');
                            $user_obj->type = 0;
                            $user_obj->remember_token = '';
                            $user_obj->created_at = date('Y-m-d H:i:s');
                            $user_obj->updated_at = date('Y-m-d H:i:s');
                            $user_obj->status = 0;
                            $user_obj->save();
                        }

                        $userdata = User::where([['simulation_id', '=', $simulation->id], ['type', '=', 0], ['color_code', '!=', '']])->get();
                        $adminuserdetails = AdminUser::where([['simulation_id', '=', $simulation->id]])->get();
                        foreach ($adminuserdetails as $key => $val) {
                            foreach ($userdata as $key => $value) {
                                $chatuser = new ChatUser;
                                $chatuser->name = $value->name . '-' . $val->name . '-' . $val->role;
                                $chatuser->simulation_id = $simulation->id;
                                $chatuser->user_id = $value->id;
                                $chatuser->main_user_id = $value->id;
                                $chatuser->admin_user_id = $val->id;
                                $chatuser->save();
                            }
                        }
                    }
                }

                if (isset($my_array5) && !empty($my_array5)) {
                    foreach ($my_array5 as $key => $value) {
                        $doc_type = Upload::select('type', 'ext')->where('id', '=', $value)->first();

                        $doc['simulation_id'] = $simulation->id;
                        $doc['upload_file_id'] = $value;
                        $doc['type'] = $doc_type->type;
                        $doc['status'] = 0;
                        $doc['ext'] = $doc_type->ext;
                        $doc['created_by'] = Auth::user()->id;
                        $doc['updated_by'] = Auth::user()->id;
                        $doc['created_at'] = date('Y-m-d H:i:s');
                        $doc['updated_at'] = date('Y-m-d H:i:s');
                        $insertdoc_arr[] = (array) $doc;
                    }
                    $insertdoc_saved = DB::table('favourite_images')->insert($insertdoc_arr);
                }

                if (isset($my_array6) && !empty($my_array6)) {
                    foreach ($my_array6 as $key => $value) {
                        $link['simulation_id'] = $simulation->id;
                        $link['upload_file_id'] = $value;
                        $link['type'] = 3;
                        $link['status'] = 0;
                        $link['ext'] = 'link';
                        $link['created_by'] = Auth::user()->id;
                        $link['updated_by'] = Auth::user()->id;
                        $link['created_at'] = date('Y-m-d H:i:s');
                        $link['updated_at'] = date('Y-m-d H:i:s');
                        $insertlink_arr[] = (array) $link;
                    }
                    $insertlink_saved = DB::table('favourite_images')->insert($insertlink_arr);
                }

                return redirect('/simulationlist')->with('success', 'Simulation updated successfully');
            } else {
                return redirect('/login');
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Simulation  $simulation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Simulation $simulation)
    {
        try {
            $simulation_id = request()->segment(2);
            $deleteuser = User::where('simulation_id', $simulation_id)->delete();
            $simulationdeleted = Simulation::where('id', $simulation_id)->delete();
            $deleteadminuser = AdminUser::where('simulation_id', $simulation_id)->delete();
            $deletechatuser = ChatUser::where('simulation_id', $simulation_id)->delete();
            $deletechatuser = Token::where('simulation_id', $simulation_id)->delete();
            return redirect('/simulationlist')->with('success', 'Simulation deleted successfully');
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function token_creation($id)
    {
        try {
            //Create the url and tokens for all users
            $simulations = Simulation::where('status', 0)->value('simulation_name');
            $user_id = User::where([['status', 0], ['simulation_id', $id], ['color_code', '!=', ''], ['type', 0]])->select('id')->get();
            $users_count = User::where([['status', 0], ['simulation_id', $id], ['color_code', '!=', ''], ['type', 0]])->count();
            //$url= URL::to("/").'/'.$simulations;
            $url = URL::to("/") . '/' . "loginuser";
            $token_data = Simulation::where(['id' => $id, 'status' => 0])->update(['url' => $url]);

            //Token Creation
            $j = 0;
            for ($i = 1; $i <= $users_count; $i++) {
                // Available alpha caracters
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

                // generate a pin based on 2 * 7 digits + a random character
                $pin = mt_rand(1000, 9999)
                    . mt_rand(1000, 9999)
                    . $characters[rand(0, strlen($characters) - 1)];

                // shuffle the result
                $string = str_shuffle($pin);
                $token = new Token;
                $token->simulation_id = $id;
                $token->user_id = $user_id[$j]['id'];
                $token->token = $string;
                $token->save();
                $j++;
            }
            $token = Simulation::where('simulations.status', 0)
                ->leftJoin('tokens as t', 't.simulation_id', '=', 'simulations.id')
                ->select('t.token', 'simulations.simulation_name', 'simulations.url')
                ->get();
            // dd($simulations);
            // $simulations=Simulation::where('status',0)->get();
            return response()->json([$token]);

            //return response()->json(['token' => $token]);
            // return "success";
        } catch (Exception $e) {
            report($e);
            return "fail";
        }
    }

    public function loginuser(Request $request)
    {
        if (Auth::check()) {
            if (Auth::user()->type == 1) {
                return redirect('/login');
            }else{
                return redirect('/private');
            }
        }
        else{
            return view('simulation.userlogin');
        }
    }

    public function loginauth(Request $request)
    {
        try {

            $token = $request->password;
            $loginsimulation_id = Token::where('tokens.token', $token)
                ->where('tokens.status', 0)
                //->where('u.welcome_status', 0)
                ->Join('users as u', 'u.id', '=', 'tokens.user_id')
                ->select('u.simulation_id', 'u.id')
                ->first();

            // if(Session::get('welcome_status') == 1){
            //     $sess = Session::get('welcome_status');
            //     //Session::put('welcome_status', 1);
            // }else{
            //     Session::put('welcome_status', 0);
            //     $sess = Session::get('welcome_status');
            // }
            // Session::put('sess_id',  $simulationid);

            if (isset($loginsimulation_id->id) && !empty($loginsimulation_id->id) && $loginsimulation_id->id != '') {
                $input['id'] = $loginsimulation_id->id;
                if (Auth::loginUsingId($input)) {
                    $succ_update = User::where('id', $loginsimulation_id->id)->update(['welcome_status' => 1, 'login_status' => 0, 'username' => $request->username]);
                    if ($succ_update) {
                        return redirect()->intended('/private');
                    } else {
                        return redirect()->intended('/loginuser/' . $token);
                    }
                } else {
                    return redirect()->intended('/loginuser/' . $token);
                }
            } else {
                return redirect()->intended('/loginuser/' . $token)->with('message', 'Token is Invalid or Already Used');
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function print_token($id)
    {
        try {
            $simulations=Simulation::where([['status',0],['id',$id]])->get(array('simulation_name','url'));
            $tokens=Token::where([['simulation_id',$id]])->get();
            $url = $simulations[0]->url;
            $simulation_name = $simulations[0]->simulation_name;

            return view('simulation.print_token',compact('url','simulation_name','tokens'));
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function leaveroom()
    {
        try {
            $simulationid = request()->segment(2);
            $succ_regenerate = Token::where('simulation_id', $simulationid)->update(['status' => 1]);
            $succ_regenerate_status = User::where('simulation_id', $simulationid)->update(['login_status' => 1, 'status' => 1]);
            Simulation::where('id', $simulationid)->update([
                'status' => 1,
                'history_summary_url' => null,
            ]);

            GenerateSimulationHistoryReportJob::dispatch($simulationid);

            Session::flash('success', 'Simulation end Successfully. Chat history report is being generated.');
            Session::flash('pending_report_id', $simulationid);
            broadcast(new ExitSimulation($simulationid))->toOthers();
            return response(['status' => 'Message sent successfully', 'message' => $simulationid]);
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function userlogout()
    {
        try {
            $token = Token::where([['user_id', Auth::user()->id]])->value('token');
            User::where('id', Auth::user()->id)->update(['welcome_status' => 0, 'status' => 0]);
            \Session::flush();

            return redirect()->intended('/loginuser/' . $token);
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    //change message delivery status

    public function delivery_status(Request $request, $id, $status)
    {
        try {
            $message_update = Message::where('id', $id)->update(['delivery_status' => $status]);
            $message = $request->message['user_id'];

            broadcast(new MessageDelivered($message))->toOthers();
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function delivery_status_click($userid)
    {
        try {
            $message = $userid;
            broadcast(new MessageDelivered($message))->toOthers();
            return "true";
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function brodcastmessage(Request $request)
    {
        try {
            $mediatype = $request->type;
            $users = array_filter(array_unique($request->usersid));
            $insert_arr = array();
            $insertreciverid_arr = array();

            $word = "https:";
            $wordone = "http:";
            $wordtwo = "www.";

            if (strpos($request->message, $word) !== false) {
                $msgval = $request->message;
                $request->message = "<a id='linkid-p' target='_blank' :href='$msgval'>" . $msgval . "</a>";
            } elseif (strpos($request->message, $wordone) !== false) {
                $msgval = $request->message;
                $request->message = "<a id='linkid-p' target='_blank' :href='$msgval'>" . $msgval . "</a>";
            } elseif (strpos($request->message, $wordtwo) !== false) {
                $msgval = $word . $request->message;
                $request->message = "<a id='linkid-p' target='_blank' :href='$msgval'>" . $msgval . "</a>";
            } else {
                $request->message = $request->message;
            }


            foreach ($users as $key => $value) {

                $recieve_id = ChatUser::where([['status', 0], ['id', $value]])->value('user_id');
                $chat_user_identification_id = ChatUser::where([['status', 0], ['id', $value]])->value('admin_user_id');
                $admin_chat_id = ChatUser::where([['status', 0], ['id', $value]])->value('id');

                $insertmessage['user_id'] = request()->user()->id;
                $insertmessage['chat_user_identification_id'] = $chat_user_identification_id;
                $insertmessage['message'] = $request->message;
                $insertmessage['created_at'] = date('Y-m-d H:i:s');;
                $insertmessage['updated_at'] = date('Y-m-d H:i:s');;
                $insertmessage['receiver_id'] = $recieve_id;

                if ($mediatype === "0") {
                    $insertmessage['video'] = $request->filepath;
                    $insertmessage['type'] = $mediatype;
                }
                if ($mediatype === "1") {
                    $insertmessage['image'] = $request->filepath;
                    $insertmessage['type'] = $mediatype;
                }
                if ($mediatype === "2") {
                    $insertmessage['doc'] = $request->filepath;
                    $insertmessage['type'] = $mediatype;
                }
                $insertmessage['upload_id'] = $request->upload_id;
                if ($request->ext != '') {
                    $insertmessage['ext'] = $request->ext;
                }
                $insertmessage['link'] = $request->link ?? '';
                $insertmessage['seen_status'] = 0;
                $insertmessage['admin_chat_id'] = $admin_chat_id;
                $insertmessage['delivery_status'] = 0;
                $insertreciverid['receiver_id'] = $recieve_id;
                $insert_arr[] = (array) $insertmessage;
                $insertreciverid_arr[] = (array) $insertreciverid;
            }
            $message_saved = DB::table('messages')->insert($insert_arr);

            if ($message_saved) {
                $oneDimensionalArray = array_map('current', $insertreciverid_arr);

                $message = array_unique($oneDimensionalArray);
                // dd($message);
                foreach ($message as $value) {
                    broadcast(new BroadcastMessageSent($value))->toOthers();
                }

                return response(['status' => 'Message sent successfully', 'message' => $request->message]);
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function regeneratepin(Request $request)
    {
        try {
            $succ_regenerate = Token::where('simulation_id', $request->simulation_id)->update(['status' => 0]);
            $succ_regenerate_status = User::where('simulation_id', $request->simulation_id)->update(['login_status' => 0, 'status' => 0]);
            return response(['status' => 'regenrate pin successfully']);
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }


    public function simulation_history()
    {
        try {
            if (Auth::user()->type == 1) {
                $simulations = Simulation::with(['chat_users' => function ($query) {
                    $query->where('status', '0');
                },
                'create_user'
                ])->where('status', '1')->orderBy('updated_at','desc')->get();
                $path = 'history';
                $pendingReportId = request('pending_report', Session::get('pending_report_id'));

                return view('simulation.simulation_history', compact('simulations', 'pendingReportId'))->with('path', $path);
            } else {
                return redirect('/login');
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    /**
     * JSON status for polling (page may load before queue job finishes).
     */
    public function simulation_history_report_status($id)
    {
        if (Auth::user()->type != 1) {
            return response()->json(['ready' => false], 403);
        }

        $simulation = Simulation::where('id', $id)->where('status', 1)->first(['id', 'history_summary_url']);

        return response()->json([
            'ready' => $simulation && !empty($simulation->history_summary_url),
            'download_url' => $simulation && !empty($simulation->history_summary_url)
                ? url('/simulation_history/report/' . $simulation->id)
                : null,
        ]);
    }

    /**
     * Redirect to a pre-signed S3 URL for the simulation summary report.
     */
    public function simulation_history_report_download($id)
    {
        try {
            if (Auth::user()->type != 1) {
                return redirect('/login');
            }

            $simulation = Simulation::where('id', $id)->where('status', 1)->first();

            if (!$simulation || empty($simulation->history_summary_url)) {
                return redirect('/simulation_history')->with('error', 'Summary report is not ready yet. Please try again shortly.');
            }

            $signedUrl = app(SimulationReportStorageService::class)
                ->temporaryDownloadUrl($simulation->history_summary_url);

            return redirect($signedUrl);
        } catch (Exception $e) {
            report($e);

            return redirect('/simulation_history')->with('error', 'Unable to download summary report.');
        }
    }

    public function simulation_history_download(Request $request)
    {
        try {
            $identify_key = $request->indication_key;

            $report_data = array();
            $datas = $request->chat_users;

            if ($identify_key == 1) {

                foreach ($datas as $user) {


                    $recieve_id = ChatUser::where([['status', 0], ['id', $user]])->value('user_id');
                    $chat_user_identification_id = ChatUser::where([['status', 0], ['id', $user]])->value('admin_user_id');
                    $simulation_id=DB::table('chat_users')
                     ->Join('simulations','simulations.id','=','chat_users.simulation_id')
                     ->select('simulations.created_by')
                     ->where([['chat_users.status',0],['chat_users.id',$user]])->first();

                    $simulation_history = Message::with('chat_users_data.simulation_data', 'user_data', 'upload_data')->where(['user_id' => $simulation_id->created_by, 'receiver_id' => $recieve_id, 'chat_user_identification_id' => $chat_user_identification_id])
                        ->orWhere(function ($query) use ($user, $recieve_id, $chat_user_identification_id) {
                            $query->where(['user_id' => $recieve_id, 'receiver_id' => auth()->id(), 'chat_user_identification_id' => $chat_user_identification_id]);
                        })
                        ->get();

                    //dd($simulation_history);
                    //$simulation_history['simulation_id']=$simulation_id->simulation_id;
                    //$simulation_history['simulation_name']=$simulation_id->simulation_name;
                    array_push($report_data, $simulation_history);
                }

                //return $report_data;
                return view('simulation.simulation_history_download', compact('report_data'));
            }
            if ($identify_key == 2) {

                $smulation_value = 0;
                foreach ($datas as $user) {

                    $simulation_id = ChatUser::where([['status', 0], ['id', $user]])->value('simulation_id');
                    if ($smulation_value != $simulation_id) {
                        $update_status = Simulation::where('id',  $simulation_id)->update(['status' => '2']);
                    }
                    $smulation_value = $simulation_id;
                }
            }
            return redirect('/simulation_history')->with('delete_success', 'Simulation Delete Successfully!');
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function nameupdate(Request $request)
    {
        try {

            $vdoname = $request->filename;
            $id = $request->itemid;
            $updatename = Upload::where('id', $id)->update(['name' => $vdoname]);
            if ($updatename) {
                return response(['status' => '1', 'value' => $vdoname]);
            } else {
                return response(['status' => '0']);
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }
    public function welcomestatus(Request $request)
    {
        try {
            // dd(Auth::user()->type);
            // dd(Session::get('welcome_status'));
            $status = $request->welcome_status;
            if (Auth::user()->type == 1) {
                $updatename = User::where('id', Auth::user()->id)->update(['welcome_status' => $status]);

                if (Session::get('welcome_status') == 0) {

                    Session::forget('welcome_status');
                    $sess = Session::put('welcome_status', 1);
                    $status = $sess;
                }
                if ($updatename) {
                    return response(['welcome_status' => 1]);
                    // return response(['welcome_status' => $status]);
                }
            } else {
                $updatename = token::where('user_id', Auth::user()->id)->update(['welcome_status' => $status]);
                if ($updatename) {
                    return response(['welcome_status' => $status]);
                }
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }
    public function statuschange(Request $request)
    {
        try {

            if ($request->roomstatus == "leaving") {
                $update_succ = User::where('name', $request->name)->where('simulation_id', $request->simulation_id)->update(['welcome_status' => 0]);
            } else if ($request->roomstatus == "joining") {
                $update_succ = User::where('name', $request->name)->where('simulation_id', $request->simulation_id)->update(['welcome_status' => 1]);
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }
    public function sess()
    {
        Session::flush();
    }
}
