@extends('layouts.admin._master')
@section("page_header_title","گزارش 1012 - گزارش وضعیت های مهم بسته بندی")

@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>گزارش 1012 - گزارش وضعیت های مهم بسته بندی</h5>
                </div>

                <div class="card-block">
                    <form id="form1" autocomplete="off" action="{{route("report.1012.submit")}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                           <div class="col-md-6">
                               <div class="row">
                                   @include("component.input.datepicker._datepicker",["id"=>"start_date","lable"=>"از تاریخ ","class_col"=>"col-md-10"])

                                   @include("component.input._time",["id"=>"start_time","lable"=>"  ساعت ","m"=>"0","h"=>"0"])

                                   @include("component.input.datepicker._datepicker",["id"=>"end_date","lable"=>" تا تاریخ ","class_col"=>"col-md-10"])

                                   @include("component.input._time",["id"=>"end_time","lable"=>"  ساعت ","m"=>0,"h"=>0])

                                   <div class="col-md-10">
                                       @include("component.input._select",[
                                           "id"=>"goods_kind_id",
                                           "label"=>"رسته کالایی  ",
                                           "option"=>$goods_kind_option["items"],
                                           "val"=>$goods_kind_option["value"],
                                           "text"=>$goods_kind_option["text"],
                                           "class_col"=>""
                                           ])
                                   </div>

                                   <input type="hidden" id="leading_false" value="1">

                               </div>
                           </div>

                        </div>
                        <br/>
                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary"> مشاهده گزارش </button>

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
                "start_date_value":"required",
                "end_date_value":"required",
                "product_id_auto":"required",
            }
        });
    </script>
@endsection
