@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("fabric_raw.machine.launch_change_lot.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        @include("goods_kind_process.fabric_raw.public._shift_and_counter")
                        @include("component.input._text",["id"=>"carrier_id","label"=>"شماره غلطک جدید پارچه خام"])

                    <?php $i = 0;?>
                        @foreach($machine_input_share_band as $item)
                            @include("component.input._text",["id"=>"lot_".($item->id)."_code","label"=>"لات ".$item->material->caption??""])
                        @endforeach
                        <div class="col-md-12">
                            <a href="{{route("fabric_raw.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success">تایید راه اندازی تغییر کالیته</button>
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
                "carrier_id": "required",

                @foreach($machine_input_share_band as $item)
                "lot_{{$item->id}}_code": "required",
                @endforeach
            }
        });
    </script>
@endsection
