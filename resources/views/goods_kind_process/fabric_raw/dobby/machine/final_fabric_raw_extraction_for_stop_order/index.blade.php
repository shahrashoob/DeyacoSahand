@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>استخراج پارچه پایانی (دستور توقف)   {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("fabric_raw.machine.final_fabric_raw_extraction_for_stop_order.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="w-100"></div>



                        @include("goods_kind_process.fabric_raw.public._shift_and_counter")

                        <div class="col-md-12">
                            <a href="{{route("fabric_raw.machine.dashboard.view",$machine)}}" class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success">ثبت فرم</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>


    </div>

@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "shift_work_id_auto": "required",
                "contour_1_value": "required",
                "contour_2_value": "required",
                "contour_3_value": "required",

            }
        });
    </script>
@endsection
