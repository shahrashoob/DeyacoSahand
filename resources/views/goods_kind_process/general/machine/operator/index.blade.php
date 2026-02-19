@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت اپراتور برای ماشین {{$machine->fullCaption()}}
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route($route_path."submit",$machine)}}" method="post"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"operator_id",
                                    "label"=>" نام شاغل ",
                                    "option"=>$worker_option["items"],
                                    "val"=>"",
                                    "text"=>"",
                                    "class_col"=>""
                                    ])
                            </div>

                            @if($machine->machine_type->machine_type_consumption_type_id == 1 )
                                @include("goods_kind_process.fabric_raw.public._shift_and_counter")
                            @endif
                        </div>



                        <button type="submit" class="btn btn-primary"> ثبت اپراتور</button>
                        <a href="{{route($dashboard_route."view",$machine)}}" class="btn btn-outline-dark">بازگشت</a>

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
                "operator_id_auto": "required"
            }
        });
    </script>
@endsection
