@extends('layouts.dashboardheader') @section('content')
<section class="mainScreen dbScreen">
   <div class="dashboardArea">
      <div class="userHead">
         <div class="detBox">
            <div class="loginBox">
               <ul class="list-unstyled">
                  <li>
                     <button
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        >
                     Log out
                     </button>
                  </li>
                  @include('modules.adminmodulelogoutform')
                  <li>Welcome {{ str_replace(' ', '-', ucwords(strtolower(auth()->user()->name))) }}</li>
                  <li>
                     <figure>
                        <img
                           class="img-fluid"
                           src="{{
                           asset('userassets/images/user.png')
                           }}"
                           /><span></span>
                     </figure>
                  </li>
               </ul>
            </div>
         </div>
      </div>
   <div id="pusher-p" class="alert d-none">
	<span class="closebtn">&times;</span> 
	<strong>Success!</strong> Pusher configuration saved successfully.
</div>
<div id="generate-zip-p" class="alert d-none">
	<span class="closebtn">&times;</span> 
	<strong>Success!</strong> Generate Zip successfully.
</div>
<div id="ip-config-p" class="alert d-none">
	<span class="closebtn">&times;</span> 
	<strong>Success!</strong> Ip configuration Saved successfully.
</div>
<div id="resetip-config-p" class="alert d-none">
	<span class="closebtn">&times;</span> 
	<strong>Success!</strong> Successfully updated.
</div>

      <div class="simulationWrp">
         <div class="si__Head">
            <h2>Pusher Configuration</h2>
         </div>
         <div class="settingsWrp ovScroll__wrp">
            <div class="settingsForm">
               <div class="row">
                  <div class="col-md-6 col-12">
                     <div class="form-group">
                        <label class="col-form-label">app_id</label>
                        <input
                           id="appidnew-p"
                           class="form-control"
                           value="{{ $settingsdata[0]->app_id or '' }}"
                           /> 
                        <small id="field-validation-appidnew"  class="d-none error"></small>
                     </div>
                  </div>
                  <div class="col-md-6 col-12">
                     <div class="form-group">
                        <label class="col-form-label">key</label>
                        <input
                           id="keynew-p"
                           class="form-control"
                           value="{{ $settingsdata[0]->app_key or '' }}"
                           />
                        <small id="field-validation-keynew" class="d-none error"></small>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6 col-12">
                     <div class="form-group">
                        <label class="col-form-label">secret</label>
                        <input
                           id="secretnew-p"
                           class="form-control"
                           value="{{ $settingsdata[0]->secret or '' }}"
                           />
                        <small id="field-validation-secretnew" class="d-none error"></small>
                     </div>
                  </div>
                  <div class="col-md-6 col-12">
                     <div class="form-group">
                        <label class="col-form-label">cluster</label>
                        <input
                           id="cluster-new-p"
                           class="form-control"
                           value="{{ $settingsdata[0]->cluster or '' }}"
                           />
                        <small id="field-validation-clusternew" class="d-none error"></small>
                     </div>
                  </div>
               </div>
               <div class="set__btnGrp2">
                  <button id="find-pusher-con-p">Save</button>
               </div>
            </div>
            <div class="row m-0">
               <div class="col-md-6 col-sm-6 col-12 pl-0">
                  <div class="si__Head">
                     <h2>IP configuration</h2>
                  </div>
                  <div class="settingsForm ipbox open">
                     <div class="form-group">
                        <label class="col-form-label">IP Address</label>
                        <div class="row">
                           <div class="col-12 col-md-7 col-lg-8">
                           <input
                              id="ipaddress-new-p"
                              class="form-control"
                              value="{{$ipdata[0]->ip_address or ''}}"
                              />   
                           </div>
                           <div class="col-12 col-md-5 col-lg-4">
                           <div class="set__btnGrp2 ">
                              <button id="find-ip-address-p">Save</button>
                              <button id="reset-ip-address-p">Reset</button>
                           </div>
                        </div>
                           <small id="field-validation-ipaddress" class="d-none error"></small>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-6 col-sm-6 col-12 pr-0">
                  <div class="si__Head">
                     <h2>Generate Zip</h2>
                  </div>
                  <div class="settingsForm ipbox open">
                       
                     <div class="set__btnGrp2">
                           <img id="generate-loder-p" class="p-1 d-none"style="width:10%;height:5%"src= "userassets/images/spinner.gif" alt=”animated”/>
                        <button id="genarate-zip-p" class="">
                        Genarate
                        </button>
                        <a
                           id="download-zip-p"
                           class="d-none"
                           href="/uploads.zip"
                           download
                           >
                        <button>Download</button></a
                           >
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
@endsection @section('sectionfooter')
<script src="{{ asset('js/settings.js') }}" defer></script>
@endsection