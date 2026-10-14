<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--dashboardheaderscript-->
    @include('inc.dashboardheaderscript')
<!--dashboardheaderscript-->
   <body>
      <header>
    <!--dashboardnavemenu-->
    @include('inc.dashboardnavmenu')
    <!--dashboardnavemenu-->
      </header>
      @yield('content')
    <footer>
    <!--dashboardfooterscript-->
    @include('inc.dashboardfooterscript')
    <!--dashboardfooterscript-->
    <!--pagefooterscript-->
    @yield('sectionfooter')
    <!--pagefooterscript-->
    </footer>
   </body>
</html>