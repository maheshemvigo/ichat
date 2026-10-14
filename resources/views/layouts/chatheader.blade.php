<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
      <meta  name="csrf-token" content="{{ csrf_token() }}">
      
      <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
      <meta name="description" content="">
      <meta name="author" content="">
      <title>{{ config('app.name', 'Paton Messaging Services') }}</title>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" type="text/css" href="{{asset('userassets/css/bootstrap-new.min.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('userassets/css/fontawesome-all.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('userassets/css/font-awesome.css')}}">
      <link rel="stylesheet" href="{{asset('userassets/css/style.css')}}" type="text/css">
      <link rel="stylesheet" type="text/css" href="{{asset('userassets/css/developer.css')}}">
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800&display=swap" rel="stylesheet">
   </head>
   <body>
      <header>
         <div class="topBar">
         @if(Auth::user()->type == 1)
         <span class="redirect-back-p">
            <div class="headLogo">
            <img style="width: 78px;padding: 4px;" class="img-fluid" src="{{ asset('userassets/images/logowhite.svg')}}">
              <!-- // Logo Niro -->
              <h3>Paton Messaging Service</h3>
            </div>
            </span>
            @else
            <span class="">
            <div class="headLogo">
            <img style="width: 78px;padding: 4px;" class="img-fluid" src="{{ asset('userassets/images/logowhite.svg')}}">
              <!-- // Logo Niro -->
              <h3>Paton Messaging Service</h3>
            </div>
            </span>
            @endif
         </div>
      </header>
        <div id="app">
            @yield('content')
        </div>
      <footer>
      </footer>
        <script src="{{ asset('userassets/js/jquery.min.js')}}"></script>
        <script src="{{ asset('userassets/js/snavvy.js')}}"></script>
        <script src="{{ asset('userassets/js/bootstrap.bundle-new.js')}}"></script>      
        {{-- <script src="{{ asset('userassets/js/notify.js')}}"></script> --}}
        <script src="{{ asset('userassets/js/classie.js')}}"></script>
        <script src="{{ asset('userassets/js/jquery-smoothscroll.min.js')}}"></script>
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-smooth-scroll/2.2.0/jquery.smooth-scroll.min.js"></script> --}}
        <script src="{{ asset('userassets/js/custom.js')}}" ></script>
        <script src="{{ asset('js/app.js') }}" defer></script>
        @yield('sectionfooter')
   </body>
</html>