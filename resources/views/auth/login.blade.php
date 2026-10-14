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
      <!--<link rel="stylesheet" type="text/css" href="css/snavvy.css">-->
      <link rel="stylesheet" type="text/css" href="{{asset('userassets/css/jquery-ui.css')}}">
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
                  <img class="img-fluid " src="/userassets/images/Logo.png">
               </div>
               <div class="LoginBox">
                  <div class="wcmhead">
                     <h1>Welcome Back!</h1>
                     <h6>Login <span>to get started.</span></h6>
                  </div>
                  <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                     @csrf
                     <div class="ip_box">
                        <div class="form-group">
                           <label>Username</label>
                           {{-- <input type="email" class="form-control"> --}}
                           <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>
                           @if ($errors->has('email'))
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $errors->first('email') }}</strong>
                           </span>
                           @endif
                        </div>
                     </div>
                     <div class="ip_box">
                        <div class="form-group">
                           <label>Password</label>
                           {{-- <input type="password" class="form-control"> --}}
                           <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                           @if ($errors->has('password'))
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $errors->first('password') }}</strong>
                           </span>
                           @endif
                        </div>
                     </div>
                     <div class="ip_box">
                        <ul class="list-unstyled checklist checklist2">
                           <li>
                              <label>Remember me
                              <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                              <span class="checkmark"></span>
                              </label>
                           </li>
                        </ul>
                     </div>
                     <div class="btnGrp">
                        {{-- <button class="LoginBtn">Login</button> --}}
                        <button type="submit" class="LoginBtn">
                        {{ __('Login') }}
                        </button>
                        {{-- <button class="forgotbtn">Forgot Password?</button> --}}
                        <a class="forgotbtn" href="{{ route('password.request') }}">
                        {{ __('Forgot Password?') }}
                        </a>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </section>
      <script src = "{{asset('userassets/js/jquery.min.js')}}"></script>
      <!--<script src="js/bootstrap.min.js"></script>-->
      <script src = "{{asset('userassets/js/bootstrap.bundle-new.js')}}"></script>
      <script src = "{{asset('userassets/js/classie.js')}}"></script>
      {{-- <script src = "{{asset('userassets/js/jquery-smoothscroll.min.js')}}"></script> --}}
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-smooth-scroll/2.2.0/jquery.smooth-scroll.min.js"></script>
      <script src = "{{asset('userassets/js/custom.js')}}"></script>
   </body>
</html>