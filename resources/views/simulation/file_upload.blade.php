@extends('layouts.dashboardheader')
@section('content')
@include('confirm')
<section class="mainScreen dbScreen">
   <div class="dashboardArea">
      <div class="userHead">
         <div class="detBox">
            <div class="loginBox">
               <ul class="list-unstyled">
                  <li>
                     <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log out</button>
                  </li>
                  @include('modules.adminmodulelogoutform')
                  <li>Welcome {{ str_replace(' ', '-', ucwords(strtolower(auth()->user()->name))) }}</li>
                  <li>
                     <figure>
                        <img class="img-fluid" src="{{ asset('userassets/images/user.png')}}"><span></span>
                     </figure>
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
         <div class="alert alert-error alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Error! </strong>{{ Session::get('error') }}
         </div>
      @endif
      <div class="simulationWrp">
         <div class="si__Head">
         @if(auth()->user()->admin_type === 'super')
            <h2>File Uploader</h2>
            <div class="si__btnGrp">
               <button id="hide-show-class-p" style="background-color: #44C97D">Upload</button>
            </div>
            @endif
         </div>
         <div class="mediaBoard ovScroll__wrp row">
            <div class="col-12">
               <div id="upload-section-p" class="d-none">
                  <form method="post" action="{{url('/fileupload_store')}}" enctype="multipart/form-data" class="dropzone" id="dropzoneForm">
                     @csrf
                  </form>
                  <div id="priview"></div>
                  <div class="format-msg-p">Accepted document formats (pdf, doc, docx) (File size :300 MB)</div>
                  <div class="file-btns">
                     <button type="button" style="min-width: 67px; min-height: 28px;" class="save-info" id="submit-all">Ok</button>
                     <button id="cancelUploadButton" style="min-width: 67px; min-height: 28px; margin-left:10px; display: none;" class="save-info">Cancel</button>
                  </div>
                  <div class="clearfix"></div>
               </div>
               <form>
                  @if(!empty($files) && $files->count() > 0)
                     <div class="mediadetBoxes row">
                        @foreach($files as $file)
                           <?php
                              $path_name = $file->path_name;
                              $ext = pathinfo($path_name, PATHINFO_EXTENSION);
                           ?>
                           <div class="mediadetBox col-12 col-sm-6 col-md-4 col-lg-3">
                              <div class="media__item">
                                 @if($ext == 'doc' || $ext == 'docx' )
                                    <a href="{{ asset('/uploads/fileupload') }}/{{$file->path_name}}" target="_blank">
                                       <img src="{{ asset('/uploads') }}/doc_file_display_image.jpg" alt="No Image Found" style="width:237px;height:200px;">
                                    </a>
                                 @elseif($ext == 'pdf')
                                    <a href="{{ asset('/uploads/fileupload') }}/{{$file->path_name}}" target="_blank">
                                       <img src="{{ asset('/uploads') }}/pdf_file_display_image.jpg" alt="No Image Found" style="width:237px;height:200px;">
                                    </a>
                                 @else
                                    <a href="{{ asset('/uploads/fileupload') }}/{{$file->path_name}}" target="_blank">
                                       <img src="{{ asset('/uploads') }}/file_display_image.png" alt="No Image Found" style="width:237px;height:200px;">
                                    </a>
                                @endif
                              </div>
                              <div class="form-group get-click">
                              @if(auth()->user()->admin_type === 'super')
                                 <input class="form-control savename-p" data-cr="{{$file->id}}" data-placement="bottom" data-toggle="tooltip" title="Tooltip on bottom" disabled value="{{$file->name}}">
                                 <a href="" data-id="{{ $file->id }}" class="btn-delete-file"><img src="/userassets/images/dlt.svg" width="15px;"></a>
                                 @else
                                 <input class="form-control" data-cr="{{$file->id}}" data-placement="bottom" data-toggle="tooltip" title="Tooltip on bottom" disabled value="{{$file->name}}">
                                 @endif
                              </div>
                           </div>
                        @endforeach
                     </div>
                  @else
                     <div class="cmnCl no-item-available">
                        No documents available.
                     </div>
                  @endif

                  {{-- <button type="button" class="save-info" id="submitname-p">Upload</button> --}}
               </form>
            </div>
         </div>
      </div>
   </div>
</section>
@endsection
@section('sectionfooter')
<script src="{{ asset('js/file_upload.js') }}" defer></script>
<script src="{{ asset('js/file.js') }}" defer></script>
@endsection
