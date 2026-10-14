<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Ipconfig;
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
use Response;
use App\Favourite;
use App\Upload;
use Zipper;
use App\Zip;


class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $settingsdata = Setting::select('*')->where('created_by', Auth::user()->id)->get();
            $ipdata = Ipconfig::select('*')->where('created_by', Auth::user()->id)->get();
            $path = 'setting';
            return view('simulation.settings', compact('settingsdata', 'ipdata', 'path'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }
    public function saveipaddress(Request $request)
    {
        try {
            $selectvalues = Ipconfig::select('*')->where('created_by', Auth::user()->id)->get();
            if (count($selectvalues) > 0) {
                $ipchange = Ipconfig::where('created_by', Auth::user()->id)->update(['ip_address' => $request->ipaddress]);
            } else {
                $ipconfig = new Ipconfig;
                $ipconfig->ip_address = $request->ipaddress;
                $ipconfig->port = '8000';
                $ipconfig->status = 0;
                $ipconfig->created_by = Auth::user()->id;
                $ipconfig->updated_by = Auth::user()->id;
                $ipchange = $ipconfig->save();
            }
            if ($ipchange) {
                return response()->json(['message' => 'success', 'ipaddress' => $request->ipaddress]);
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }

        // dd($request->ipaddress);
        //$_SERVER['REMOTE_ADDR']

    }
    public function pushercre(Request $request)
    {
        try {
            $selectvalues = Setting::select('*')->where('created_by', Auth::user()->id)->get();
            if (count($selectvalues) > 0) {
                $settingchange = Setting::where('created_by', Auth::user()->id)->update(['app_id' => $request->app_id, 'app_key' => $request->app_key, 'secret' => $request->secret, 'cluster' => $request->cluster]);
            } else {
                $setting = new Setting;
                $setting->app_id = $request->app_id;
                $setting->app_key = $request->app_key;
                $setting->secret = $request->secret;
                $setting->cluster = $request->cluster;
                $setting->status = 0;
                $setting->created_by = Auth::user()->id;
                $setting->updated_by = Auth::user()->id;
                $settingchange = $setting->save();
            }
            if ($settingchange) {
                return response()->json(['message' => 'success', 'app_id' => $request->app_id, 'app_key' => $request->app_key, 'secret' => $request->secret, 'cluster' => $request->cluster]);
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }
    public function handlezip(Request $request)
    {
        try {
            $selectvalues = Zip::select('*')->where('created_by', Auth::user()->id)->get();
            if (count($selectvalues) > 0) {

                $files = public_path('uploads');
                $suss = \Zipper::make(public_path('uploads.zip'))->add($files)->close();

                $newzip = Zip::where('created_by', Auth::user()->id)->update(['status' => 0]);
            } else {

                $files = public_path('uploads');
                $suss = \Zipper::make(public_path('uploads.zip'))->add($files)->close();

                $setting = new Zip;
                $setting->status = 0;
                $setting->created_by = Auth::user()->id;
                $setting->updated_by = Auth::user()->id;
                $suss = $setting->save();
            }
            if ($newzip) {
                return response()->json(['message' => 'success']);
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }

        // return response()->download(public_path('uploads.zip'));
        // $this->info('folder ziped successfully!');
    }
    public function resetipaddress(Request $request)
    {
        $res = Ipconfig::where('created_by', Auth::user()->id)->delete();
        if ($res) {
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'fail']);
        }
    }
}
