@extends('layouts.admin._master')

@section("page_header_title","کارتابل  منابع انسانی ")


@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ثبت درخواست اضافه کاری گروهی </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.worker.overtime_together.submit")}}" method="post" autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">



                            <div class="w-100"></div>
                            @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"start_datetime","hasTime"=>1,"lable"=>" تاریخ شروع (کوچکتر تاریخ به ازای همه پرسنل) ","class_col"=>"col-md-3","value"=>""])
                            <div class="w-100"></div>
                            @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"end_datetime","hasTime"=>1,"lable"=>" تاریخ پایان (بزرگترین تاریخ به ازای همه پرسنل) ","class_col"=>"col-md-3","value"=>""])
                            <div class="w-100"></div>
                            @include("component.input._textarea",["id"=>"text","lable"=>" توضیحات ","class_col"=>"col-md-3","value"=>""])
                            <div class="w-100"></div>
                            <div class="col-md-12" data-select2-id="119">

                                @include("component.input.select2._select2",[
                               "id"=>"user_ids",
                               "label"=>" لیست پرسنل جهت اضافه کاری ",
                               "option"=>$option_users,
                               "class_col"=>""
                               ])
                            </div>
                        </div>



                        <button type="submit" onclick="return confirm('آیا از ثبت اضافه کار گروهی اطمینان دارید؟')" class="btn btn-primary"> ثبت اضافه کاری گروهی</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker.jalali_datepicker._style")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    @include("component.input.select2._script")
@endsection

@section("scripts")
    @include("component.input.datepicker.jalali_datepicker._script")
    <script>
        $('#form1').validate({
            rules: {
                "leave_overtime_type_id_auto": "required",
                "end_datetime_value": "required",
                "end_time_h": "required",
                "start_datetime_value": "required",
                "start_time_h": "required",
                "text": "required"
            }
        });
    </script>
@endsection
