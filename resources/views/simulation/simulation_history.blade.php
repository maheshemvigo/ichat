@extends('layouts.dashboardheader')
@section('content')

<style>
.historyAccord .history-row-header {
  position: relative;
  min-height: 54px;
}
.historyAccord .history-row-toggle {
  width: 100%;
  text-align: left;
  padding-right: 220px !important;
}
.historyAccord .history-report-link {
  position: absolute !important;
  top: 24px !important;
  right: 68px !important;
  z-index: 112 !important;
  white-space: nowrap;
}
</style>

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
            @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success! </strong>{{ Session::get('success') }}
                </div>
            @endif
            @if(Session::has('delete_success'))
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success! </strong>Simulation deleted successfully.
                </div>
            @endif
            @if(Session::has('error'))
                <div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Error! </strong>{{ Session::get('error') }}
                </div>
            @endif

            <div class="simulationWrp">
                <div class="si__Head">
                    <h2>Simulation History</h2>
                    <div class="si__btnGrp">
                        <button type="button" id="delete" onclick="delete_validation()" style="background-color:#ff3333">Delete</button>
                        <button type="button" id="download" onclick="form_submission(1)" style="background-color: #44C97D">Download</button>
                    </div>
                </div>
                 <!-- --------------------------delete confirmation popup--------------------------- -->
                 <div class="modal fade mediaModal leaveusers-p" id="exampleModalleaveusers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                           <div class="modal-content">
                              <div class="modal-body">
                                 <h4>Do you really want to remove item from history list ?</h4>
                                  <div class="si__btnGrp">
                                    <button type="button" class="add-btn" onclick="form_submission(2)">Yes</button>
                                    <button  class="add-btn" data-dismiss="modal">No</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  <!-- --------------------------------------------------------------------------------- -->

                <div class="si__History ovScroll__wrp">

                    <div class="historyAccord">
                            {{ Form::open(array('url' => 'simulation_history_download','method'=>'post', 'id'=>'form_download')) }}
                        <div class="accordion" id="accordionExample">
                          <input type="hidden" name="indication_key" id="indication_key" value="">
                            @foreach($simulations as $key=>$simulation)

                            <div class="card child-check-all-m">
                                <div class="card-header history-row-header" id="headingOne{{$key}}">
                                    <h2 class="mb-0">
                                        <ul class="list-unstyled checklist checklist2" style="position: relative;z-index: 111;">
                                            <li>
                                                <label>
                                                    <input type="checkbox" class="check-all-m" value='collapseOne{{$key}}'>
                                                    <span class="checkmark"></span>
                                                </label>
                                            </li>
                                        </ul>

                                        <button class="btn collapsed history-row-toggle" type="button" data-toggle="collapse" data-target="#collapseOne{{$key}}" aria-expanded="true" aria-controls="collapseOne{{$key}}">
                                            <ul class="list-unstyled checklist checklist2">
                                                <li>
                                                    <label>{{$simulation->simulation_name}}{{'-'}}{{$simulation->create_user && $simulation->create_user->name ? $simulation->create_user->name : ''}}{{'-'}}
                                                        @if($simulation->create_user && $simulation->create_user->admin_type == 'super') <b style="color:#e74c3c"> super admin </b>
                                                        @elseif($simulation->create_user)  <b style="color:#3498db"> sub admin</b>
                                                        @endif
                                                        {{' '}}
                                                        <?php
                                                        $date=date('d-m-Y', strtotime($simulation->created_at));
                                                        $time=date('H:i:s', strtotime($simulation->created_at));
                                                        ?>
                                                        <small>{{$date}} - {{$time}}</small>
                                                    </label>
                                                </li>
                                            </ul>
                                            <span class="ang"><img src="{{asset('/')}}userassets/images/angle2.svg"></span>
                                        </button>
                                    </h2>

                                    @php
                                        $isPendingReport = empty($simulation->history_summary_url)
                                            && !empty($pendingReportId)
                                            && (string) $pendingReportId === (string) $simulation->id;
                                    @endphp
                                    @if(!empty($simulation->history_summary_url) || $isPendingReport)
                                    <span class="history-report-link" data-simulation-id="{{ $simulation->id }}"@if($isPendingReport) data-poll-report="1"@endif>
                                        @if(!empty($simulation->history_summary_url))
                                            <a href="{{ url('/simulation_history/report/'.$simulation->id) }}" style="background:#44C97D;color:#fff;padding:6px 14px;border-radius:4px;font-size:13px;text-decoration:none;" onclick="event.stopPropagation();">Download Summary Report</a>
                                        @else
                                            <span class="history-report-pending" style="color:#888;font-size:13px;">Summary report processing…</span>
                                        @endif
                                    </span>
                                    @endif
                                </div>

                            <div id="collapseOne{{$key}}" class="collapse " aria-labelledby="headingOne{{$key}}" >
                                    <div class="card-body child-checkbox-tick-m">

                                        <ul class="list-unstyled checklist checklist3">

                                            @foreach($simulation->chat_users as $chat_user)
                                            <li>
                                                <label>{{$chat_user->name}}
                                                <input type="checkbox" name="chat_users[]" value="{{$chat_user->id}}" class="child-check-box-m collapseOne{{$key}}Child">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </li>

                                            @endforeach


                                        </ul>

                                    </div>

                                </div>

                            </div>


                            @endforeach
                        </div>
                        {{ Form::close() }}

                    </div>
                </div>
            </div>

        </div>
    </section>
    @include('confirm')
@endsection
@section('sectionfooter')
<script>

$(document).on('change','.check-all-m',function() {
        if(this.checked) {
          selectAll(this.value)
        }else{
            UnSelectAll(this.value);
        }
    });

    function selectAll(data) {
        var items = document.getElementsByClassName(data+'Child');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }

    function UnSelectAll(data) {
        var items = document.getElementsByClassName(data+'Child');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }
    function form_submission($value) {
       var value =$value;
       //Assign value to text field for identify its for download
        document.getElementById("indication_key").value =value;

        let checked_value=false;
        $('.child-check-box-m').each(function (index, value) {

            if($(this).is(':checked'))
            {
                checked_value=true;

            }

        });
        if(checked_value==true)
            {
                document.getElementById("form_download").submit();// Form submission
            }
            else{
                var modalFooter = '<div class="modal-body">   <h4>Please select atleast one simulation</h4>' +
                '  <div class="si__btnGrp"><button class="add-btn btn-danger" data-dismiss="modal">Okay</button></div>\n' +
                '            </div>';
                var modal = $("#confirm_modal");
                modal.find(".modal-title").remove();
                modal.find(".modal-footer").remove();
                modal.find(".modal-header").remove();
                modal.find(".modal-body").html("").html(modalFooter);
                $("#confirm_modal").modal("show");
                return null;
            }
  }
  function delete_validation()
  {
    let checked_value=false;
        $('.child-check-box-m').each(function (index, value) {


            if($(this).is(':checked'))
            {
                checked_value=true;

            }

        });
        if(checked_value==true)
            {
                $('#exampleModalleaveusers').modal('toggle');
                $('#exampleModalleaveusers').modal('show');
            }
            else{
                var modalFooter = '<div class="modal-body">   <h4>Please select atleast one simulation</h4>' +
                '  <div class="si__btnGrp"><button class="add-btn btn-danger" data-dismiss="modal">Okay</button></div>\n' +
                '            </div>';
                var modal = $("#confirm_modal");
                modal.find(".modal-title").remove();
                modal.find(".modal-footer").remove();
                modal.find(".modal-header").remove();
                modal.find(".modal-body").html("").html(modalFooter);
                $("#confirm_modal").modal("show");
                return null;
            }

  }
  function form_submission_delete(){


  }

  // Poll only the simulation just ended (query param, session flash, or data-poll-report).
  (function () {
    var params = new URLSearchParams(window.location.search);
    var pendingId = params.get('pending_report') || @json(!empty($pendingReportId) ? (string) $pendingReportId : null);
    var $container = $('[data-poll-report="1"]');

    if (!$container.length && pendingId) {
      $container = $('.history-report-link[data-simulation-id="' + pendingId + '"]');
    }

    if (!$container.length || !$container.find('.history-report-pending').length) {
      return;
    }

    if (!pendingId) {
      pendingId = String($container.data('simulation-id'));
    }

    var attempts = 0;
    var maxAttempts = 180; // ~15 minutes at 5s intervals (large reports can take several minutes)

    function checkPendingReport() {
      if (!$container.find('.history-report-pending').length) {
        return;
      }

      $.getJSON('/simulation_history/report-status/' + pendingId, function (data) {
        if (data.ready && data.download_url) {
          $container.html(
            '<a href="' + data.download_url + '" style="background:#44C97D;color:#fff;padding:6px 14px;border-radius:4px;font-size:13px;text-decoration:none;" onclick="event.stopPropagation();">Download Summary Report</a>'
          );
        }
      });
    }

    checkPendingReport();
    var pollTimer = setInterval(function () {
      attempts++;
      if (attempts >= maxAttempts || !$container.find('.history-report-pending').length) {
        clearInterval(pollTimer);
        return;
      }
      checkPendingReport();
    }, 5000);
  })();
</script>

@endsection
