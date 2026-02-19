@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تعویض چله {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("fabric_raw.machine.change_warps.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        @include("goods_kind_process.fabric_raw.public._shift_and_counter")

                        <?php $i = 0;?>
                        @foreach($lot_list as $item)
                            @include("component.input._number",["id"=>"lot_".(++$i)."_code","label"=>"لات ".$item->material->caption??""])
                        @endforeach
                        <div class="col-md-12">
                            <a href="{{route("fabric_raw.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success">تایید تعویض چله</button>
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
                "lot_1_code": "required",
                "lot_2_code": "required",
                "lot_3_code": "required",
                "lot_4_code": "required",
            }
        });
    </script>
@endsection
