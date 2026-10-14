<?php

namespace App\Http\Controllers;

use App\Upload;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Session;
use DB;
use App\Links;

class UploadsController extends Controller
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
      $videos = Upload::where([['type', 0], ['status', 0]])->orderBy('id', 'DESC')->get();
      $path = 'video';
      $inpath = 'video';
      return view('simulation.video_upload', compact('videos', 'path', 'inpath'));
    } catch (Exception $e) {
      report($e);

      return false;
    }
  }

  public function imageupload_index()
  {
    try {
      $images = Upload::where([['type', 1], ['status', 0]])->orderBy('id', 'DESC')->get();
      $path = 'video';
      $inpath = 'image';
      return view('simulation.image_upload', compact('images', 'path', 'inpath'));
    } catch (Exception $e) {
      report($e);

      return false;
    }
  }

  public function fileupload_index()
  {
    try {
      $files = Upload::where([['type', 2], ['status', 0]])->orderBy('id', 'DESC')->get();
      $path = 'video';
      $inpath = 'file';
      return view('simulation.file_upload', compact('files', 'path', 'inpath'));
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
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Upload  $upload
   * @return \Illuminate\Http\Response
   */
  public function show(Upload $upload)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Upload  $upload
   * @return \Illuminate\Http\Response
   */
  public function edit(Upload $upload)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Upload  $upload
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Upload $upload)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Upload  $upload
   * @return \Illuminate\Http\Response
   */
  public function destroy(Upload $upload)
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function storevideo(Request $request)
  {
    try {
      $upload_arr = array();

      foreach ($request->file('profile') as $file) {
        $getimageName = rand() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/videoupload'), $getimageName);

        $upload_details['path_name'] = $getimageName;
        $upload_details['name'] = rand();
        $upload_details['type'] = 0;
        $upload_details['status'] = 0;
        $upload_details['ext'] = $file->getClientOriginalExtension();
        $upload_details['created_by'] = Auth::user()->id;
        $upload_details['updated_by'] = Auth::user()->id;
        $upload_details['created_at'] = date('Y-m-d H:i:s');
        $upload_details['updated_at'] = date('Y-m-d H:i:s');
        $upload_arr[] = (array) $upload_details;
      }

      $stock_saved = DB::table('upload_files')->insert($upload_arr);

      if ($stock_saved) {
        Session::flash('success', 'Added successfully.');
        return response(['status' => '1']);
      } else {
        Session::flash('error', 'Failed');
      }
    } catch (Exception $e) {
      report($e);

      return false;
    }
  }

  public function imageupload_store(Request $request)
  {
    try {
      $upload_arr = array();
      $i = 1;
      foreach ($request->file('profile') as $file) {
        $filename_index = "filename" . $i;
        $upload_details['ext'] = $file->getClientOriginalExtension();
        $filename =  !is_null($request->$filename_index) ? $request->$filename_index . "." . $upload_details['ext'] : $file->getClientOriginalName();
        $getimageName = $file->getClientOriginalName();
        $file->move(public_path('uploads/imageupload'), $getimageName);
        $upload_details['path_name'] = $getimageName;
        $upload_details['name'] = $filename;
        $upload_details['type'] = 1;
        $upload_details['status'] = 0;
        $upload_details['created_by'] = Auth::user()->id;
        $upload_details['updated_by'] = Auth::user()->id;
        $upload_details['created_at'] = date('Y-m-d H:i:s');
        $upload_details['updated_at'] = date('Y-m-d H:i:s');
        $upload_arr[] = (array) $upload_details;
        $i++;
      }

      $stock_saved = DB::table('upload_files')->insert($upload_arr);
      if ($stock_saved) {
        Session::flash('success', 'Added successfully.');
        return response(['status' => '1']);
      } else {
        Session::flash('error', 'Failed');
      }
    } catch (Exception $e) {
      report($e);

      return false;
    }
  }

  public function fileupload_store(Request $request)
  {
    try {
      $upload_arr = array();
      $i = 1;
      foreach ($request->file('profile') as $file) {
        $filename_index = "filename" . $i;
        $upload_details['ext'] = $file->getClientOriginalExtension();
        $filename =  !is_null($request->$filename_index) ? $request->$filename_index . "." . $upload_details['ext'] : $file->getClientOriginalName();
        $getimageName = $file->getClientOriginalName();
        $file->move(public_path('uploads/fileupload'), $getimageName);

        // $videoName = time().'.'.$file->getClientOriginalExtension();
        // $filename = $file->getClientOriginalName();
        // $filePath = 'videoupload/'.$videoName;
        // $t = Storage::disk('s3')->put($filePath, file_get_contents($file));
        // $imageName = Storage::disk('s3')->url('videoupload/'.$videoName);

        $upload_details['path_name'] = $getimageName;
        $upload_details['name'] = $filename;
        $upload_details['type'] = 2;
        $upload_details['status'] = 0;
        $upload_details['ext'] = $file->getClientOriginalExtension();
        $upload_details['created_by'] = Auth::user()->id;
        $upload_details['updated_by'] = Auth::user()->id;
        $upload_details['created_at'] = date('Y-m-d H:i:s');
        $upload_details['updated_at'] = date('Y-m-d H:i:s');
        $upload_arr[] = (array) $upload_details;
        $i++;
      }
      $stock_saved = DB::table('upload_files')->insert($upload_arr);
      if ($stock_saved) {
        Session::flash('success', 'Added successfully.');
        return response(['status' => '1']);
      } else {
        Session::flash('error', 'Failed');
      }
    } catch (Exception $e) {
      report($e);

      return false;
    }
  }

  public function delete(Request $request)
  {
    try {
      $file = Upload::find($request->id);

      if (isset($file) && !empty($file)) {
        if ($file->type == 2 && file_exists("uploads/fileupload/" . $file->path_name)) {
          unlink("uploads/fileupload/" . $file->path_name);
        } elseif ($file->type == 1 && file_exists("uploads/imageupload/" . $file->path_name)) {
          unlink("uploads/imageupload/" . $file->path_name);
        } else {
          if (file_exists("uploads/videoupload/" . $file->path_name))
            unlink("uploads/videoupload/" . $file->path_name);
        }
        $file->delete();

        return back()->with("success", "File deleted successfully.");
      }
    } catch (Exception $e) {
      report($e);

      return false;
    }
  }

  public function links()
  {
    try {
      $links = Links::where('status', 0)->orderBy('id', 'DESC')->get();
      $path = 'video';
      return view('simulation.links', compact('links', 'path'));
    } catch (Exception $e) {
      report($e);

      return false;
    }
  }

  public function addLink(Request $request)
  {
    try {
      $validator = \Validator::make(\Request::all(), [
        'url' => 'required|url'
      ]);
      if ($validator->fails()) {
        return back()->with("error", "Link cannot be added");
      } else {
        $upload_arr['link'] = $request->url;
        $upload_arr['name'] = $request->name ?? rand();
        $upload_arr['created_by'] = Auth::user()->id;
        $upload_arr['updated_by'] = Auth::user()->id;
        $upload_arr['created_at'] = date('Y-m-d H:i:s');
        $upload_arr['updated_at'] = date('Y-m-d H:i:s');

        $link_saved = Links::insert($upload_arr);
        if ($link_saved) {
          return back()->with("success", "Link added successfully.");
        } else {
          return back()->with("error", "Link cannot be added");
        }
      }
    } catch (Exception $e) {
      report($e);
      return false;
    }
  }

  public function deleteLink(Request $request)
  {
    try {
      $link = Links::find($request->id);

      if (isset($link) && !empty($link)) {
        Links::where('id', $link->id)->update(['status' => 1]);
        return back()->with("success", "Link deleted successfully.");
      }
    } catch (Exception $e) {
      report($e);
      return "fail";
    }
  }

  public function linkupdate(Request $request)
  {

    try {
      $name = $request->linkname;
      $id = $request->id;
      $updatename = Links::where('id', $id)->update(['name' => $name]);
      if ($updatename) {
        return response(['status' => '1', 'value' => $name]);
      } else {
        return response(['status' => '0']);
      }
    } catch (Exception $e) {
      report($e);
      return "fail";
    }
  }
}
