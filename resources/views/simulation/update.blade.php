@extends('layouts.dashboardheader')
@section('content')
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
      <input id="ipaddress-set-p" type="hidden" value="{{ $ipaddress->ip_address or '' }}">
      {{ Form::open(['id'=>'createform-p', 'action' => 'SimulationController@update','files'=>true]) }}
      <div class="simulationWrp">
         <div class="si__Head">
            <h2>Edit Simulation</h2>
            <div class="si__btnGrp">
               <button id="savesimulation-p" type="button" style="background-color: #44C97D;" >Update</button>
               <button id="cancel-update-p">Cancel</button>
            </div>
         </div>
         <div class="si__add">
            <div class="si__NameWrp">
               <div class="si__Name">
                  <label>Simulation Name</label>
                  <div class="form-group">
                     <!-- <input class="form-control" placeholder="Enter simulation name"> -->
                     <input type="text" class="form-control" id="Simulationion_name" placeholder="Enter Simulation Name" name="simulation_name" value="{{$simulation_name->simulation_name}}" autocomplete="off" required>
                     <small class="field-validation-simname d-none error"></small>
                     <small class="field-validation-simname-p d-none error"></small>

                     <input id ="user-cre-p" type="hidden" value="{{$user}}">
                     <input id="simulation-id-p" type="hidden" name="simulation_id" value="{{$simulation_id}}">
                  </div>
                  @if ($errors->has('simulation_name'))
                  <div style="color:red;font-size:12px">*{{ $errors->first('simulation_name') }}</div>
                  @endif
               </div>
               <div class="si__Terminals">
                  <label>Terminals</label>
                  <select class="select-cmn" id="user_number">
                     <option value="AL">Select</option>
                     <option value="1">1</option>
                     <option value="2">2</option>
                     <option value="3">3</option>
                     <option value="4">4</option>
                     <option value="5">5</option>
                     <option value="6">6</option>
                     <option value="7">7</option>
                     <option value="8">8</option>
                     <option value="9">9</option>
                     <option value="10">10</option>
                     <option value="11">11</option>
                     <option value="12">12</option>
                     <option value="13">13</option>
                     <option value="14">14</option>
                     <option value="15">15</option>
                   <!--  <option value="16">16</option>
                     <option value="17">17</option>
                     <option value="18">18</option>
                     <option value="19">19</option>
                     <option value="20">20</option>
                     <option value="21">21</option>
                     <option value="22">22</option> -->
                  </select>
                  <small class="field-validation-terminalcount d-none error"></small>
               </div>
            </div>
            <div class="si__Settings">
               <div class="si__Admin cmnCl">
                  <div class="dB__head">
                     <h3>Admin Settings</h3>
                     <a id="add-user-button-p" class="upload-btn add-btn">
                     <label>
                     Add <span><img src="{{ asset('userassets/images/plus.svg')}}"></span>
                     </label>
                     </a>
                  </div>
                  <div class="admin__table">
                     <table id="user-listing-p" class="table">
                        <thead>
                           <tr>
                              <th>Name</th>
                              <th>Position</th>
                              <th>Profile Picure</th>
                              <th>Remove</th>
                           </tr>
                        </thead>
                        <tbody>
                           @php $i=0; @endphp
                           @if(isset($admin_user_details) && !empty($admin_user_details))
                           @foreach($admin_user_details as $details)
                           @php $i++; @endphp
                           <tr class="user-tr-p">
                              <td>
                                 <input type="text" class="form-control name-p" placeholder="Enter Simulation Name" name="Simulationion_user_name[]" value="{{$details->name}}" required>
                                 <small class="field-validation-name d-none error"></small>
                              </td>
                              <td>
                                 <input type="text" class="form-control role-p" data-user="3" placeholder="Enter Simulation Role" name="Simulationion_role[]" value="{{$details->role}}" required>
                                 <small class="field-validation-rolname d-none error"></small>
                              </td>
                              <td class="imagestyle-p">
                                 <input type="file" data-user="profile-img-tag{{$i}}" class="profilepic-p profile-img d-none" name="profilepic[]">
                                 <input class="profile-img-tag" type="hidden" name="profilepicname[]" value="{{$details->image}}">
                                 @if($details->image !='')           
                                 <span class="span-remove-org-p"><img  class="img-fluid chack-update-p upload-id-click-p" src="/uploads/{{$details->image}}" style="height:100%"></span>
                                 @endif
                                 <span  class="span-remove-dem-p d-none"><img id="profile-img-tag{{$i}}" class="img-fluid upload-id-click-p" src="" style="height:100%"></span>
                                 <small class="field-validation-pic d-none error"></small>
                              </td>
                              <td class="delete-user-p">
                                 <img src="{{URL::to('/')}}/userassets/images/dlt.svg">
                              </td>
                           </tr>
                           @endforeach
                           @endif
                        </tbody>
                     </table>
                  </div>
               </div>
               <div class="si_Color cmnCl">
                  <div class="dB__head">
                     <h3>Terminal Colours</h3>
                  </div>
                  <div id="terminal-p" class="clrPickwrp">
                  @if(isset($userterminal_details) && !empty($userterminal_details))
                     @foreach($userterminal_details as $details)
                     <div class="clrBox remove-color-picker-p">
                        <label>{{$details->name}}</label>
                        <input class="form-control" type="color" id="head-0" data-id="{{$details->name}}" name="mainuser_color[]" value="{{$details->color_code}}">
                        <input type="hidden" id="user-0" name="mainuser[]" value="{{$details->name}}">
                     </div>
                     @endforeach
                     @endif
                  </div>
               </div>
            </div>
            <div class="si__Upload">
               {{-- <div class="videoUpload cmnCl">
                  <div class="dB__head">
                     <h3><img src="{{ asset('userassets/images/up1.svg')}}">Upload Video</h3>
                     <a class="upload-btn add-btn video-select-p">
                     <label  data-toggle="modal" data-target="#video_upload_Modal" for="file-1">
                     Add <span><img src="{{ asset('userassets/images/plus.svg')}}"></span>
                     </label>
                     </a>
                  </div>
                  <div class="videoContent upContent">
                     <div id="mediaboxwrp-p" class="mediaboxwrp">
                     @if(isset($favourite_video) && !empty($favourite_video))
                        @foreach ($favourite_video as $video)
                        <div class="mediaBox remove-box-p">
                           <video class="vdo-stayle-class-p">
                           @if (isset($ipaddress->ip_address) && $ipaddress->ip_address != '')
                              <source src="http://{{$ipaddress->ip_address}}/uploads/videoupload/{{$video->path_name}}">
                              @else
                              <source src="{{URL::to('/')}}/uploads/videoupload/{{$video->path_name}}">
                           @endif
                           </video>
                           <input type="hidden" name="docid[]" value="{{$video->upload_file_id}}">
                        </div>
                        @endforeach
                        @endif
                     </div>
                     <!-- <div class="noFile">
                        No videos available
                        </div> -->
                  </div>
               </div> --}}
               <div class="linkUpload cmnCl">
                  <div class="dB__head">
                     <h3><img src="{{ asset('userassets/images/links.svg')}}" width="20px;">Upload Video Link</h3>
                     <a class="upload-btn add-btn link-select-p">
                        <label  data-toggle="modal" data-target="#link_upload_Modal" for="file-1">
                           Add <span><img src="{{ asset('userassets/images/plus.svg')}}"></span>
                        </label>
                     </a>
                  </div>
                  <div class="linkContent upContent">
                     <ul id="mediaboxwrplink-p" class="list-unstyled">
                        @if(isset($favourite_link) && !empty($favourite_link))
                           @foreach($favourite_link as $link)
                           <li class="remove-box-p">{{$link->name}}<img class="remove-box-p" src="{{ asset('userassets/images/close.svg')}}">
                              <input type="hidden" name="linkid[]" value="{{$link->id}}">
                           </li>
                           @endforeach
                        @endif
                     </ul>
                  </div>
               </div>
               <div class="documentUpload cmnCl">
                  <div class="dB__head">
                     <h3><img src="{{ asset('userassets/images/up2.svg')}}">Upload Document</h3>
                     <a class="upload-btn add-btn doc-select-p">
                        <label  data-toggle="modal" data-target="#document_upload_Modal" for="file-1">
                        Add <span><img src="{{ asset('userassets/images/plus.svg')}}"></span>
                        </label>
                        </button>
                  </div>
                  <div class="docContent upContent">
                  <ul id="mediaboxwrpdoc-p" class="list-unstyled">

                  @if(isset($favourite_doc) && !empty($favourite_doc))
                  @foreach ($favourite_doc as $doc)
                  <li class="remove-box-p">{{$doc->name}}<img class="remove-box-p" src="{{URL::to('/')}}/userassets/images/close.svg">
                  <input type="hidden" name="docid[]" value="{{$doc->upload_file_id}}"></li>
                  @endforeach   
                  @endif
                  </ul>
                  </div>
               </div>
               <div class="imageUpload cmnCl">
                  <div class="dB__head">
                     <h3><img src="{{ asset('userassets/images/up3.svg')}}">Upload Images</h3>
                     <a class="upload-btn add-btn image-select-p">
                        <label  data-toggle="modal" data-target="#image_upload_Modal" for="file-1">
                        Add <span><img src="{{ asset('userassets/images/plus.svg')}}"></span>
                        </label>
                     </a>
                  </div>
                  <div class="imgContent upContent">
                     <div id="mediaboxwrpimage-p" class="mediaboxwrp">
                        @if(isset($favourite_image) && !empty($favourite_image))
                           @foreach ($favourite_image as $image)
                              <div class="mediaBox remove-box-p">
                                 @if (isset($ipaddress->ip_address) && $ipaddress->ip_address != '')
                                    <img class="vdo-stayle-class-p" src="http://{{$ipaddress->ip_address}}/uploads/imageupload/{{$image->path_name}}" alt="{{$image->name}}">
                                 @else
                                    <img class="vdo-stayle-class-p" src="{{URL::to('/')}}/uploads/imageupload/{{$image->path_name}}" alt="{{$image->name}}">
                                 @endif
                                 <input type="hidden" name="docid[]" value="{{$image->upload_file_id}}">
                              </div>
                           @endforeach  
                        @endif
                     </div>
                  </div>
               </div>               
            </div>
         </div>
      </div>
      {{ Form::close() }}
   </div>
</section>
@endsection
@section('sectionfooter')
<!--Image uploads Modal -->
<div class="modal fade mediaModal" id="image_upload_Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <form>
               <div class="form-group">
                  <input id="target-image" class="form-control" placeholder="Search">
               </div>
            </form>
            <h4>Images</h4>
            <div class="historyAccord">
               <ul id="img-searchresult-panel-p" class="list-unstyled checklist checklist3">
               @if(isset($images)&&!empty($images))
                  @foreach($images as $image)
                  <li>
                     <label>{{$image->name}}
                     <input class="image-details-hidden-p"  type="checkbox">
                     <input class="image-details-p" type="hidden" data-id="{{$image->id}}" data-originalname="{{$image->name}}" value="{{$image->path_name}}">
                     <span class="checkmark"></span>
                     </label>
                  </li>
                  @endforeach
                  @endif
               </ul>
            </div>
            <button id="image-selection-p" class="add-btn" data-dismiss="modal">ADD</button>
         </div>
      </div>
   </div>
</div>
<!--Documents uploads Modal -->
<div class="modal fade mediaModal" id="document_upload_Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <form>
               <div class="form-group">
                  <input id="target-doument" class="form-control" placeholder="Search">
               </div>
            </form>
            <h4>Documents</h4>
            <div class="historyAccord">
               <ul id="doc-searchresult-panel-p" class="list-unstyled checklist checklist3">
               @if(isset($documents) && !empty($documents))
                  @foreach($documents as $document)
                  <li>
                     <label>{{$document->name}}
                     <input class="doc-details-hidden-p" type="checkbox">
                     <input class="doc-details-p" type="hidden" data-id="{{$document->id}}" data-originalname="{{$document->name}}" value="{{$document->path_name}}">
                     <span class="checkmark"></span>
                     </label>
                  </li>
                  @endforeach
               @endif
               </ul>
            </div>
            <button id="doc-selection-p" class="add-btn" data-dismiss="modal">ADD</button>
         </div>
      </div>
   </div>
</div>
<!--Video uploads Modal -->
<div class="modal fade mediaModal" id="video_upload_Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <form>
               <div class="form-group">
                  <input id="target" type="text" class="form-control" placeholder="Search">
               </div>
            </form>
            <h4>Videos</h4>
            <div class="historyAccord">
               <ul id="searchresult-panel-p" class="list-unstyled checklist checklist3 searchresult-panel-p">
               @if(isset($videos) && !empty($videos))
                  @foreach($videos as $video)
                  <li>
                     <label>{{$video->name}}
                     <input class="vdo-details-hidden-p" type="checkbox">
                     <input class="vdo-details-p" type="hidden" data-id="{{$video->id}}" data-originalname="{{$video->name}}" value="{{$video->path_name}}">
                     <span class="checkmark"></span>
                     </label>
                  </li>
                  @endforeach
                  @endif
               </ul>
            </div>
            <button id="video-selection-p" class="add-btn" data-dismiss="modal">ADD</button>
         </div>
      </div>
   </div>
</div>
<!--Links uploads Modal -->
<div class="modal fade mediaModal" id="link_upload_Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-body">
            <form>
               <div class="form-group">
                  <input id="target-link" class="form-control" placeholder="Search">
               </div>
            </form>
            <h4>Links</h4>
            <div class="historyAccord">
               <ul id="link-searchresult-panel-p" class="list-unstyled checklist checklist3">
               @if(isset($links) && !empty($links))
                  @foreach($links as $link)
                  <li>
                     <label>{{$link->name}}
                        <input class="link-details-hidden-p" type="checkbox">
                        <input class="link-details-p" type="hidden" data-id="{{$link->id}}" data-originalname="{{$link->name}}" value="{{$link->name}}">
                        <span class="checkmark"></span>
                        </label>
                  </li>
                  @endforeach
                  @endif
               </ul>
            </div>
            <button id="link-selection-p" class="add-btn" data-dismiss="modal">ADD</button>
         </div>
      </div>
   </div>
</div>
<script>
   $(function(){
      handlepageslbss.initUpdateSimulation();
   });
   $(document).on('click','#cancel-update-p',function(e){
      e.preventDefault();
      window.location.href = '/simulationlist';
   })
</script>
@endsection