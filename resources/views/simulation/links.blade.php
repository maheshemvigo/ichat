@extends('layouts.dashboardheader')
@section('content')
@include('confirm')
<section class="mainScreen dbScreen">
   <div class="dashboardArea">
      <div class="userHead">
         <div class="detBox">
            <div class="loginBox">
               <ul class="list-unstyled">
                  <li>
                     <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log
                        out</button>
                  </li>
                  @include('modules.adminmodulelogoutform')
                  <li>Welcome {{ str_replace(' ', '-', ucwords(strtolower(auth()->user()->name))) }}</li>
                  <li>
                     <figure>
                        <img class="img-fluid" src="{{ asset('userassets/images/user.png')}}"><span></span>
                     </figure>
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
      @if(Session::has('error'))
      <div class="alert alert-error alert-dismissible">
         <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
         <strong>Error! </strong>{{ Session::get('error') }}
      </div>
      @endif
      <div class="alert alert-success alert-dismissible d-none" id="link-success">
         <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
         <strong>Success! </strong>Link name updated successfully!!
      </div>
      <div class="simulationWrp">
         <div class="si__Head">
            <h2>Video Links</h2>
            @if(auth()->user()->admin_type === 'super')
            <div class="si__btnGrp">
               <button id="hide-show-class-p" style="background-color: #44C97D">Add Link</button>
            </div>
            @endif
         </div>
         <div class="mediaBoard ovScroll__wrp row">
            <div class="col-12">
               <div id="link-section-p" class="d-none">

                  <div class="si__NameWrp">
                     <form method="post" action="{{url('/links')}}" class="w-100">
                        @csrf
                        <div class="row">
                           <div class="col-12 col-md-6">
                              <label>Url</label>
                              <div class="form-group">
                                 <input type="text" class="form-control" placeholder="Enter Url" name="url" id="link"
                                    required>
                              </div>

                           </div>
                           <div class="col-12 col-md-6">
                              <label>Name</label>
                              <div class="form-group">
                                 <input type="text" class="form-control" placeholder="Enter Name" maxlength="50" name="name" id="name">
                                 {{-- <span id="charCount">50</span> characters remaining --}}
                              </div>
                           </div>
                        </div>

                        <button type="submit" style="min-width: 67px; min-height: 28px;" class="save-info"
                           id="submit-link">Ok</button>
                     </form>
                  </div>

               </div>
               <form>
                  <div class="cmnCl links-table">
                     @if(!empty($links) && $links->count() > 0)
                     <table id="user-listing-p" class="table">
                        <thead>
                           <tr>
                              <th>#</th>
                              <th>Url</th>
                              <th>Name</th>
                              @if(auth()->user()->admin_type === 'super')
                              <th>Edit</th>
                              <th>Remove</th>
                              @endif
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($links as $i => $link)
                           <tr class="user-tr-p">
                              <td>{{ $i+1 }}</td>
                              <td>{{ $link->link }}</td>
                              <td id="{{ $link->id }}">
                                 <span class="link-name"> {{ $link->name }} </span>
                                 <input type="text" maxlength="50" name="link-name" id="link-name-{{ $link->id}}" value="{{ $link->name }}" class="form-control d-none">
                              </td>
                              @if(auth()->user()->admin_type === 'super')
                              <td>
                                 <a href="" data-id="{{ $link->id }}" class="btn-edit-link">
                                    <i class="far fa-edit" style="color: darkgrey;"></i>
                                 </a>
                                 <a href="" data-id="{{ $link->id }}" class="btn-save-link d-none">
                                    <i class="far fa-save" style="color: green;"></i>
                                 </a>
                              </td>
                              <td>
                                 <a href="" data-id="{{ $link->id }}" class="btn-delete-link">
                                    <img src="userassets/images/dlt.svg">
                                 </a>
                              </td>
                              @endif
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                     @else
                        No links available.
                     @endif
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>
@endsection
@section('sectionfooter')
<script src="{{ asset('js/links.js') }}" defer></script>
@endsection
