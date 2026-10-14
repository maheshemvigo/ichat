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
            <div class="simulationWrp">
               <div class="si__Head">
                  <h2>Videos</h2>
                  <div class="si__btnGrp">
                     <button style="background-color: #44C97D">Upload</button>
                  </div>
               </div>
               <div class="mediaBoard ovScroll__wrp">
                  <form method="post" action="{{url('/upload/store')}}" enctype="multipart/form-data" class="dropzone" id="dropzoneForm">
                  @csrf
                  </form>
                  <button type="button" class="btn btn-info" id="submit-all">Upload</button>
               </div>
               <div id="priview"></div>
            </div>
         </div>
      </section>
@endsection
@section('sectionfooter')
@endsection