@extends('layouts.chatheader')
@section('content')
    @include('modules.adminmodulelogoutform')
	@include('modules.usermodulelogoutform')
    <div class="alert regenrate-pin-p d-none">
        <span class="closebtn">&times;</span>
        <strong>Success!</strong> Successfully Regenrated pin.
    </div>
    <div class="alert endsimulation-p d-none">
        <span class="closebtn">&times;</span>
        <strong>Success!</strong> Simulation end Successfully.
    </div>
    <input id="csrf_id" type="hidden" value="{{ csrf_token() }}">
    @if(isset($simulationid) && !empty($simulationid))
        <private-chat
            :user="{{auth()->user()}}"
            :simulationid="{{ json_encode($simulationid) }}">
        </private-chat>
    @else
        <private-chat :user="{{auth()->user()}}"></private-chat>
    @endif
    @include('confirm')
@endsection
@section('sectionfooter')
    <script src="{{ asset('js/private.js') }}" defer></script>
    <link rel="stylesheet" type="text/css" href="{{asset('userassets/css/private.css')}}" scoped="scoped">
@endsection


