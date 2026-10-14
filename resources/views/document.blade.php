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
            <h2>User Guidance</h2>
         </div>
         <div class="si__add">
            <div class="si__NameWrp">
            <ul>
                  <li style="margin-left: 15px;">
                     Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi.  Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi.
                  </li></br>
                  <li style="margin-left: 15px;">
                     Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi.
                  </li></br>
                  <li style="margin-left: 15px;">
                     Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi.
                  </li> </br>
                  <li style="margin-left: 15px;">
                     Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi.
                  </li></br>
                  <li style="margin-left: 15px;">
                     Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi.
                  </li>
               </ul>
            </div>
         </div>
    </div>    
    </div>

</section>
@endsection
@section('sectionfooter')
<script></script>
@endsection