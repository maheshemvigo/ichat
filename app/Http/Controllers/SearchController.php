<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Simulation;
use App\Events\MessageDelivered;
use App\Events\BroadcastMessageSent;
use App\User;
use App\Upload;
use App\Token;
use App\ChatUser;
use App\AdminUser;
use App\Message;
use App\Favourite;
use App\Events\ExitSimulation;
use App\Links;
use Auth;
use Sesssion;
use Hash;
use DB;
use Redirect;
use Response;


class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function videosearch(Request $request)
    {

        try {
            $result = rtrim($request->video_ids, ',');
            $result = explode(",", $result);
            $search_val = $request->search;

            if ((isset($search_val)) && (!empty($search_val)) && ($search_val != '')) {
                $search_result = Upload::where('name', 'like', '%' . $search_val . '%')->where('type', 0)->whereNotIn('id', $result)->get();
            } else {
                $search_result = Upload::select('*')->where('type', 0)->whereNotIn('id', $result)->get();
            }
            return Response::json(array('search_result' => $search_result));
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function imagesearch(Request $request)
    {
        try {
            $result = rtrim($request->image_ids, ',');
            $result = explode(",", $result);
            $search_val = $request->search;

            if ((isset($search_val)) && (!empty($search_val)) && ($search_val != '')) {
                $search_result = Upload::where('name', 'like', '%' . $search_val . '%')->where('type', 1)->whereNotIn('id', $result)->get();
            } else {
                $search_result = Upload::select('*')->where('type', 1)->whereNotIn('id', $result)->get();
            }
            return Response::json(array('search_result' => $search_result));
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function documentsearch(Request $request)
    {
        try {
            $result = rtrim($request->doc_ids, ',');
            $result = explode(",", $result);
            $search_val = $request->search;

            if ((isset($search_val)) && (!empty($search_val)) && ($search_val != '')) {
                $search_result = Upload::where('name', 'like', '%' . $search_val . '%')->where('type', 2)->whereNotIn('id', $result)->get();
            } else {
                $search_result = Upload::select('*')->where('type', 2)->whereNotIn('id', $result)->get();
            }
            return Response::json(array('search_result' => $search_result));
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function featuredvideo(Request $request)
    {
        try {
            $search_val = $request->keywords;
            $simulation_id = $request->simulationid;
            if ((isset($search_val)) && (!empty($search_val)) && ($search_val != '')) {
                $vdo_simulatuon_details = Upload::join('favourite_images as f', 'f.upload_file_id', '=', 'upload_files.id')
                    ->select('*')->where('upload_files.name', 'like', '%' . $search_val . '%')
                    ->where('f.simulation_id', $simulation_id)
                    ->where('upload_files.type', 0)
                    ->get();
            } else {
                $vdo_simulatuon_details = Upload::join('favourite_images as f', 'f.upload_file_id', '=', 'upload_files.id')
                    ->select('*')->where('f.simulation_id', $simulation_id)
                    ->where('upload_files.type', 0)
                    ->get();
            }

            return response()->json($vdo_simulatuon_details);
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function featureddoc(Request $request)
    {
        try {
            $search_val = $request->keywords;
            $simulation_id = $request->simulationid;
            if ((isset($search_val)) && (!empty($search_val)) && ($search_val != '')) {
                $doc_simulatuon_details = Upload::join('favourite_images as f', 'f.upload_file_id', '=', 'upload_files.id')
                    ->select('*')->where('upload_files.name', 'like', '%' . $search_val . '%')
                    ->where(['f.simulation_id' => $simulation_id, 'f.type' => 2])
                    ->where('upload_files.type', 2)
                    ->get();
            } else {
                $doc_simulatuon_details = Upload::join('favourite_images as f', 'f.upload_file_id', '=', 'upload_files.id')
                    ->select('*')->where(['f.simulation_id' => $simulation_id, 'f.type' => 2])
                    ->where('upload_files.type', 2)
                    ->get();
            }
            return response()->json($doc_simulatuon_details);
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function featuredimg(Request $request)
    {
        try {
            $search_val = $request->keywords;
            $simulation_id = $request->simulationid;
            if ((isset($search_val)) && (!empty($search_val)) && ($search_val != '')) {
                $img_simulatuon_details = Upload::join('favourite_images as f', 'f.upload_file_id', '=', 'upload_files.id')
                    ->select('*')->where('upload_files.name', 'like', '%' . $search_val . '%')
                    ->where(['f.simulation_id' => $simulation_id, 'f.type' => 1])
                    ->where('upload_files.type', 1)
                    ->get();
            } else {
                $img_simulatuon_details = Upload::join('favourite_images as f', 'f.upload_file_id', '=', 'upload_files.id')
                    ->select('*')->where(['f.simulation_id' => $simulation_id, 'f.type' => 1])
                    ->where('upload_files.type', 1)
                    ->get();
            }
            return response()->json($img_simulatuon_details);
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function linksearch(Request $request)
    {
        try {
            $result = rtrim($request->link_ids, ',');
            $result = explode(",", $result);
            $search_val = $request->search;

            if ((isset($search_val)) && (!empty($search_val)) && ($search_val != '')) {
                $search_result = Links::where('name', 'like', '%' . $search_val . '%')->where('status', 0)->whereNotIn('id', $result)->get();
            } else {
                $search_result = Links::select('*')->where('status', 0)->whereNotIn('id', $result)->get();
            }
            return Response::json(array('search_result' => $search_result));
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function featuredlink(Request $request)
    {
        try {
            $search_val = $request->keywords;
            $simulation_id = $request->simulationid;
            if ((isset($search_val)) && (!empty($search_val)) && ($search_val != '')) {
                $link_simulation_details = Links::join('favourite_images as f', 'f.upload_file_id', '=', 'links.id')
                    ->select('*')->where('links.name', 'like', '%' . $search_val . '%')
                    ->where(['f.simulation_id' => $simulation_id, 'f.type' => 3])
                    ->orderBy('links.id', 'DESC')
                    ->get();
            } else {
                $link_simulation_details = Links::join('favourite_images as f', 'f.upload_file_id', '=', 'links.id')
                    ->select('*')->where(['f.simulation_id' => $simulation_id, 'f.type' => 3])
                    ->orderBy('links.id', 'DESC')
                    ->get();
            }
            return response()->json($link_simulation_details);
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }
}
