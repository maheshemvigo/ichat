<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
      <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
      <meta name="description" content="">
      <meta name="author" content="">
      <title>Paton Medical Group</title>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" type="text/css" href="{{asset('userassets/css/bootstrap-new.min.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('userassets/css/fontawesome-all.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('userassets/css/font-awesome.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('userassets/css/style.css')}}">
      <link rel="icon" type="image/png" sizes="32x32" href="{{asset('userassets/images/fav-icon/i.png')}}">
      <link rel="icon" type="image/png" sizes="32x32" href="{{asset('userassets/images/fav-icon/i2.png')}}">
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
      <style>
         .userloginpage-p{
         width: 100% !important;
         height: -webkit-fill-available !important;
         }
      </style>
   </head>
   <body>
      <section class="loginWrp">
         <div class="loginLeft">
            <figure class="img-wrapper">
               <img class="userloginpage-p" src="{{asset('userassets/images/logfig.jpg')}}">
            </figure>
         </div>
         <div class="loginRight">
            <div class="loginRight-in">
               <div class="loginLogo">
                  <img class="img-fluid" src="/userassets/images/Logo.png">
                  {{-- <img class="img-fluid" src="{{asset('userassets/images/logolog.jpg')}}"> --}}
               </div>
               <div class="LoginBox">
                  {{ Form::open(['action' => 'SimulationController@loginauth','files'=>true]) }}
                  {{ csrf_field() }}
                  <div class="ip_box">
                     @if (Session::has('message'))
                     <p style="color:red;font-size:12px">{{Session::get('message')}}</p>
                     @endif
                  </div>
                  <div class="ip_box">
                     <div class="form-group">
                           <input type="hidden" class="form-control" name="password" id="password"  value = "{{request()->segment(2)}}" >
                     </div>
                  </div>
                  <div class="btnGrp">
                     {{-- <button class="LoginBtn">Login</button> --}}
                     <button type="submit" class="LoginBtn">
                     {{ __('Start Chat') }}
                     </button>
                     {{-- <button class="forgotbtn">Forgot Password?</button> --}}
                     {{-- <a class="forgotbtn" href="{{ route('password.request') }}">
                     {{ __('Forgot Password?') }}
                     </a> --}}
                  </div>
                  {{ Form::close() }}
               </div>
            </div>
         </div>
      </section>
      <script src = "{{asset('userassets/js/jquery.min.js')}}"></script>
      <script src="{{ asset('userassets/js/bootstrap.bundle-new.js')}}"></script>
      {{-- <script src="{{ asset('userassets/js/jquery-smoothscroll.min.js')}}"></script> --}}
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-smooth-scroll/2.2.0/jquery.smooth-scroll.min.js"></script>
      <script src="{{ asset('userassets/js/custom.js')}}" ></script>
      <script>
         $("body").on('click','#cancel_button',function(){
           $('#username').val('');
           $('#password').val('');
         });
      </script>
   </body>
</html>