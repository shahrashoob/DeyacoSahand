@extends('layouts.admin._master')

@section('page_header_title'," کارتابل شخصی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> انتخاب {{$smart_object_type->caption}} پیش فرض </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.personal.submit_select_smart_object",[$smart_object_type])}}" method="post" autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                                <div class="col-md-6">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"smart_object_id",
                                        "label"=>$smart_object_type->caption ." پیش فرض",
                                        "option"=>$smart_object_option["items"],
                                        "val"=>$smart_object_option["value"],
                                        "text"=>$smart_object_option["text"],
                                        "class_col"=>""
                                        ])
                                </div>


                            @include("component.input._hidden",["id"=>"back_route","value"=>$back_route,"class_col"=>"col-md-4"])
                            @include("component.input._hidden",["id"=>"param1","value"=>$param1,"class_col"=>"col-md-4"])


                        </div>

                        <a href="{{route($back_route,[$param1])}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت </button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "default_label_printer_id_auto": "required",
                "default_printer_id_auto": "required",
            }
        });
    </script>
@endsection
