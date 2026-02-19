@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")
    @include("hr.employment.register.personal.academic_degree._list",["panel_type"=>"register"])
@endsection
