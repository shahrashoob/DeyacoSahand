@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <form id="form1" action="{{route("fabric_raw.machine.edit_log.submit",[$machine,$machine_log])}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>ویرایش لاگ ماشین {{$machine->fullCaption()}}
                        </h5>
                    </div>
                    <div class="card-block">

                        <div class="w-100"></div>
                        <div class="col-sm-6">
                            @include("component.input._aotocomplet2",[
                                "id"=>"shift_work_id",
                                "label"=>" شیفت کاری   ",
                                "option"=>$shift_work_option["items"],
                                "val"=>$shift_work_option["value"],
                                "text"=>$shift_work_option["text"],
                                "class_col"=>""
                                ])
                        </div>
                        @include("component.input._text",["id"=>"contour_1_value","value"=>$machine_log->contour_1_value,"label"=>"مقدار قطب A"])
                        @include("component.input._text",["id"=>"contour_2_value","value"=>$machine_log->contour_2_value,"label"=>"مقدار قطب B"])
                        @include("component.input._text",["id"=>"contour_3_value","value"=>$machine_log->contour_3_value,"label"=>"مقدار قطب C"])
                        @include("component.input._text",["id"=>"contour_4_value","value"=>$machine_log->contour_4_value,"label"=>"مقدار قطب D"])
                        @include("component.input._text",["id"=>"contour_5_value","value"=>$machine_log->contour_5_value,"label"=>"مقدار قطب E"])

                    </div>

                </div>

            </div>
            <div class="col-md-12">
                <a href="{{route("fabric_raw.machine.log.index",$machine)}}"
                   class="btn btn-outline-dark">بازگشت</a>

                <button type="submit" class="btn btn-success">ذخیره اطلاعات</button>
            </div>

        </div>
    </form>
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

