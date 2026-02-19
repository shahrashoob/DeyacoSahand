@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بسته بندی")

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> کنترل کیفیت بسته بندی {{$packing_form->getCode()}}   </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.packing_form.quality_control.submit_partner",[$packing_form])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        <div class="w-100"></div>

                        <div class="table-responsive">

                            @include("component.input.select2._select2",[
                                "id"=>"user_ids",
                                "label"=>"پرسنل همکار",
                                "option"=>$worker_option["items"],
                                "val"=>$worker_option["value"],
                                "text"=>$worker_option["text"],
                                "class_col"=>"col-md-4"
                                ])

                            <div class="col-100">
                            </div>


                            @include("component.input._aotocomplet2",[
                                "id"=>"packing_type_id",
                                "label"=>"نوع بسته بندی   ",
                                "option"=>$packing_type_option["items"],
                                "val"=>$packing_type_option["value"],
                                "text"=>$packing_type_option["text"],
                                "class_col"=>"col-md-4"
                                ])


                            <div class="col-md-12">
                                <a href="{{route("fabric_raw.packing_form.view",$packing_form)}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-success">تایید و ادامه</button>
                            </div>


                        </div>

                    </form>
                </div>

            </div>

        </div>
    </div>

@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    @include("component.input.select2._script")
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "packing_type_id_auto": "required",
                "user_ids": "required",

            }
        });
    </script>
@endsection
