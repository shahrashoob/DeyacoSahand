@extends('layouts.admin._master')

@section('page_header_title',"داشبورد ماشین آلات")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> پایان قفسه گذاری {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("warps.karl_mayer.machine.end_of_shelving.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="w-100"></div>

                        @if($machine->check_inventory_for_allocation)
                            @include("goods_kind_process.general.machine.injection_of_material._injection")
                        @else
                            @include("goods_kind_process.warps.karl_mayer.machine.end_of_shelving._input_yarn")
                        @endif

                        @if(count($production->packing_types) == 1)
                            @include("component.input._hidden",["id"=>"packing_type_id","value"=>$production->packing_types()->first()->packing_type_id])
                        @else
                            <div class="col-md-6">
                                @include("component.input._select",[
                                            "id"=>"packing_type_id",
                                            "label"=>" نوع بسته بندی  ",
                                            "option"=>$packing_type_option["items"],
                                            "val"=>$packing_type_option["value"],
                                            "text"=>$packing_type_option["text"],
                                            "class_col"=>""
                                            ])
                            </div>
                        @endif
                        <br/>
                        <div class="col-md-12">
                            <a href="{{route("warps.karl_mayer.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success">ثبت پایان قفسه گذاری</button>
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
