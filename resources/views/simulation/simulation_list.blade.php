@extends('layouts.dashboardheader')
@section('content')
<section class="mainScreen dbScreen stimulation-list-wrp">
   <div class="dashboardArea">
      <div class="userHead">
         <div class="detBox">
            <div class="loginBox">
               <ul class="list-unstyled">
                  <li><button onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log out</button></li>
                  @include('modules.adminmodulelogoutform')
                  <li>Welcome {{ str_replace(' ', '-', ucwords(strtolower(auth()->user()->name))) }}</li>
                  <li>
                     <figure><img class="img-fluid" src="{{ asset('userassets/images/user.png')}}"><span></span></figure>
                  </li>
               </ul>
            </div>
         </div>
      </div>
     
      <div class="simulationWrp">
         <div class="si__Head">
            <h2>Simulation List</h2>
         </div>
         <div class="" id="app">
            <div v-if="loading == 'true'" class="simulation_loader">
               <img src="/userassets/images/loading-gif.gif" alt="loading" width ="50" />
             </div>
               <simulation-list :session-value="'{{ Session::get('success') }}'" :loading="true"></simulation-list>
            </div>
      </div>
   </div>
</section>
@endsection
@section('sectionfooter')
  <script src="{{ asset('js/app.js') }}" defer></script>
@endsection