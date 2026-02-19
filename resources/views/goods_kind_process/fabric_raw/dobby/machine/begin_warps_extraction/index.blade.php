@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> شروع استخراج چله {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("fabric_raw.machine.begin_warps_extraction.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        @include("goods_kind_process.fabric_raw.public._shift_and_counter")


                        @foreach($warps_request->items as $item)
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"item_".$item->id,
                                    "label"=>"درجه ".$item->product->fullCaption()." - ورودی ".$item->input_line_code,
                                    "option"=>$degree_option["items"],
                                    "val"=>"",
                                    "text"=>"",
                                    "class_col"=>""
                                    ])
                            </div>

                        @endforeach
                        <div class="col-md-12">
                            <a href="{{route("fabric_raw.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success">تایید شروع استخراج چله</button>
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
                @foreach($warps_request->items as $item)
                "item_{{$item->id}}_auto": "required",
                @endforeach
            }
        });
    </script>
@endsection
