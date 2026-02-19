@extends('layouts.admin._master')

@section('page_header_title'," ")

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>فرم ارزیابی عملکرد تولید</h5>
            </div>
            <div class="card-block">
                <form id="form1" action="{{route("production.submit_form1",$production)}}" method="post" novalidate="novalidate">
                    @csrf

                    @include('production.production_card._info_small')



                    <div class="row">


                        @include("component.input._aotocomplet2",[
                                "id"=>"supervisor_worker_id",
                                "label"=>"سرپرست تولید",
                                "text_white"=>1,
                                "option"=>$worker_option["items"],
                                "val"=>$worker_option["value"],
                                "text"=>$worker_option["text"],
                                ])



                    @include("component.input._aotocomplet2",[
                        "id"=>"line_id",
                        "label"=>" کد خط",
                        "text_white"=>1,
                        "option"=>$line_option["items"],
                        "val"=>$line_option["value"],
                        "text"=>$line_option["text"],
                        ])


                        @include("component.input._number",["id"=>"set_up_time","lable"=>" زمان ست آپ  (دقیقه) ","value"=>$production->set_up_time])

                        @include("component.input._number",["id"=>"unemployment_time","lable"=>" زمان مجاز بی کاری (دقیقه) ","value"=>$production->unemployment_time])


                        @include("component.input._number",["id"=>"down_time","lable"=>" دون تایم خط (دقیقه) ","value"=>$production->down_time])



                        @include("component.input._number",["id"=>"number_product","lable"=>"تعداد تولید شده ","value"=>$production->number_product])

{{--                        @include("component.input._number",["id"=>"sub_number_product","lable"=>" تعداد تکی ","value"=>$production->sub_number_product])--}}

                    </div>

                    <a href="{{route("production.view_card",$production)}}" class="btn btn-outline-dark">بازگشت</a>
                    <button type="submit" class="btn btn-primary"> ثبت کارت و مرحله بعد</button>
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
                shift_time:{
                    required:1,
                    number:1
                } ,
                set_up_time: {
                    required:1,
                    number:1
                } ,
                unemployment_time: {
                    required:1,
                    number:1
                } ,
                down_time: {
                    required:1,
                    number:1
                } ,
                line_code: {
                    required:1,
                    number:1
                } ,
                start_time_m: "required",
                start_time_h: "required",
                end_time_m: "required",
                end_time_h: "required",
                number_product: {
                    required:1,
                    number:1
                } ,
                sub_number_product:{
                    required:1,
                    number:1
                } ,
                date_of_production_date: "required",
                supervisor_worker_id_auto:"required",
                line_id_auto:"required",
            }
        });
</script>
@endsection
