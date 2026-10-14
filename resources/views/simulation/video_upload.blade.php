@extends('layouts.dashboardheader')
@section('content')
@include('confirm')
<section class="mainScreen dbScreen">
   <div class="dashboardArea">
      <div class="userHead">
         <div class="detBox">
            <div class="loginBox">
               <ul class="list-unstyled">
                  <li><button onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log out</button></li>
                  @include('modules.adminmodulelogoutform')
                  <li>Welcome {{auth()->user()->name}}</li>
                  <li>
                     <figure><img class="img-fluid" src="{{ asset('userassets/images/user.png')}}"><span></span></figure>
                  </li>
               </ul>
            </div>
         </div>
      </div>
      @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success! </strong>{{ Session::get('success') }}                      
                </div>
            @endif
            @if(Session::has('error'))
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Error! </strong>{{ Session::get('error') }}                      
                </div>
      @endif
      <div class="simulationWrp">
         <div class="si__Head">
            <h2>Video Uploader</h2>
            <div class="si__btnGrp">
               <button id="hide-show-class-p" style="background-color: #44C97D">Upload</button>
            </div>
         </div>
         <div class="mediaBoard ovScroll__wrp row">
            <div class="col-12">
               <div id="upload-section-p" class="d-none">
               <form method="post" action="{{url('/upload/storevideo')}}" enctype="multipart/form-data" class="dropzone" id="dropzoneForm">
                  @csrf
               </form>
               <div id="priview"></div>
               <div class="format-msg-p">Accepted video formats (mp4, mkv) (File size :300 MB)</div>
               <div class="file-btns">
                  <button type="button" style="min-width: 67px; min-height: 28px;" class="save-info" id="submit-all">Ok</button>
                  <button id="cancelUploadButton" style="min-width   : 67px; min-height: 28px; margin-left:10px; display: none;" class="save-info">Cancel</button>
               </div>
               <div class="clearfix"></div>
               </div>
               <form>
                  @if(!empty($videos) && $videos->count() > 0)
                     <div class="mediadetBoxes row">
                        @foreach($videos as $video)
                           <div class="mediadetBox col-12 col-sm-6 col-md-4 col-lg-3">
                              <div class="media__item">
                                    <video width="100%" height="200px" controls>
                                    <source src="{{ asset('/uploads/videoupload') }}/{{$video->path_name}}" type="video/mp4">
                                    <source src="{{ asset('/uploads/videoupload') }}/{{$video->path_name}}" type="video/webm">           
                                          Your browser does not support the video tag.
                                       </video> 
                              </div>
                              <div class="form-group get-click">
                                    <input class="form-control savename-p" data-cr="{{$video->id}}" data-placement="bottom" data-toggle="tooltip" title="Tooltip on bottom" disabled value="{{$video->name}}">
                                    <a href="" data-id="{{ $video->id }}" class="btn-delete-file"><img src="/userassets/images/dlt.svg" width="15px;"></a>
                              </div>
                           </div>
                        @endforeach
                     </div>
                  @else
                     <div class="cmnCl no-item-available">
                        No videos available.
                     </div>
                  @endif
               </form>
            </div>
         </div>
      </div>
   </div>
</section>
@endsection
@section('sectionfooter')
<script src="{{ asset('js/video_upload.js') }}" defer></script>
<script src="{{ asset('js/file.js') }}" defer></script>
@endsection