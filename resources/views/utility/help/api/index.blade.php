@extends('layouts.admin._master',["no_persian"=>1])

@section("page_header_title","راهنمای وب سرویس ها ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست وب سرویس ها </h5>
                </div>

                <div class="card-body">
                    <h5><a href="{{route("utility.help.api.fabric_raw_quality_control")}}"><span
                                class="fa fa-info"></span> راهنمای وب سرویس پارچه خام </a></h5>
                </div>

            </div>
        </div>
    </div>
@endsection

