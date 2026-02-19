@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>افزودن رسته کالایی به
                        <b>
                        {{ $machine_type_output_band->caption}}
                        </b>
                        از گروه ماشین
                        <b>
                            {{$machine_type->caption}}
                        </b>
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form2"
                          action="{{route("line_product_station.machine_type.output_band.add_goods_kind_submit_step1",$machine_type_output_band)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="col-md-6">
                            <br/>
                            <br/>
                            @include("component.input._aotocomplet2",[
                                "id"=>"goods_kind_id",
                                "label"=>" افزودن جنس کالای خروجی  ",
                                "option"=>$goods_kind_option["items"],
                                "class_col"=>""
                                ])
                        </div>


                        <button type="submit" class="btn btn-primary"> افزودن</button>

                    </form>

                    <div class="row">
                    </div>
                </div>
            </div>
        </div>
        <div>
            <a href="{{route("line_product_station.machine_type.output_band.index",$machine_type)}}"
               class="btn btn-outline-dark">بازگشت</a>
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
                "active_status_id_auto": "required",
                "line_input_number": "required",
            }
        });
        $('#form2').validate({
            rules: {
                "goods_kind_id_auto": "required",
            }
        });
    </script>
@endsection
