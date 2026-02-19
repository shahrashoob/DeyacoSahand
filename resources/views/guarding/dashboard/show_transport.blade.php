@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])
@section("page_header_title","داشبورد نگهبانی")

@section('content')

    @include("utility.transport.dashboard._show_transport",["route_url"=>route("guarding.dashboard.index")])

@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                x: "required",
            }
        });
    </script>
    @include("component._spinner",["id"=>'.btn-danger'])
@endsection
