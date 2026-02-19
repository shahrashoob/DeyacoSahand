@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> شروع برگردان {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("warps.matthys.machine.start_beaming.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="w-100"></div>

                        @if( count( $packing_type_option["items"] ) != 2 )
                            @include(
                            "component.input._select",
                            ["id"=>"packing_type_id",
                            'label'=>"نوع بسته بندی",
                            "option"=>$packing_type_option["items"],
                            "val"=>$packing_type_option["value"],
                            "text"=>$packing_type_option["text"]
                        ])
                        @else
                            @include("component.input._hidden",["id"=>"packing_type_id","value"=>$packing_type_option["items"][1]["value"]])

                        @endif
                        @include("component.input._number",["id"=>"carrier_code","label"=>"شماره حامل ","value"=>""])



                        <div class="col-md-12">
                            <a href="{{route("warps.matthys.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success">ثبت  شروع برگردان</button>
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
                "carrier_code": "required",
                "packing_type_id": "required"
            }
        });
    </script>
@endsection
