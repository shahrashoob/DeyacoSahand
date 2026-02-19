@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")
    @include("hr.employment.register.address._preview",["panel_type"=>"register"])

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection