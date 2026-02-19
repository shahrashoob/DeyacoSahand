@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")
    @include("hr.employment.register.personal_type.".$employment->personal_type_id.".cooperation_type.".$employment->cooperation_type_id."._preview_info")
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection