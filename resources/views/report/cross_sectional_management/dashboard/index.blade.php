@extends('layouts.admin._master')
@section("page_header_title"," تولید / داشبورد مقطعی مدیریت")

@section("content")
    <div class="row">

        <div class="col-sm-12">


            <div class="card">
                <div class="card-header">
                    <h5> داشبورد مقطعی مدیریت</h5>
                </div>

                <div class="card-block">
                    <form id="form1" autocomplete="off"
                          action="{{route("report.cross_sectional_management.dashboard.submit")}}" method="post"
                          novalidate="novalidate">
                        @csrf

                      <div class="row">
                          <div class="col-md-6">
                              <div class="row">
                                  @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["hasTime"=>"","id"=>"start_date","lable"=>"از تاریخ ","class_col"=>"col-md-10","value"=>$start_date])

                                  @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["hasTime"=>"","id"=>"end_date","lable"=>" تا تاریخ ","class_col"=>"col-md-10","value"=>$end_date])

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
                                  <div class="w-100"><br/></div>
                                  <div class="col-md-10">
                                      @include("component.input._select",[
                                          "id"=>"classification_id",
                                          "label"=>"طبقه بندی ",
                                           "option"=>$classification_option["items"],
                                          "val"=>$classification_option["value"],
                                          "text"=>$classification_option["text"],
                                          "class_col"=>""
                                          ])
                                  </div>
                                  <div class="w-100"><br/></div>
                                  <div class="col-md-10">
                                      @include("component.input._select",[
                                          "id"=>"report_break_type_id",
                                          "label"=>"نوع تفکیک گزارش ",
                                           "option"=>$report_break_option["items"],
                                          "val"=>$report_break_option["value"],
                                          "text"=>$report_break_option["text"],
                                          "class_col"=>""
                                          ])
                                  </div>

                              </div>
                          </div>
                          <div class="col-md-6">
                             <h5> لیست گزارش ها:</h5>
                              <div class="w-100"></div>
                              @include("component.input._checkbox_simple",["id"=>"report[1009]","label"=>"میزان کالای استخراج شده"])
                              <div class="w-100"></div>
                              @include("component.input._checkbox_simple",["id"=>"report[1010]","label"=>"شاخص های بهره وری"])
                              <div class="w-100"></div>
                              @include("component.input._checkbox_simple",["id"=>"report[1011]","label"=>"میزان کالای تولید شده"])
                              <div class="w-100"></div>
                              @include("component.input._checkbox_simple",["id"=>"report[1013]","label"=>"گزارش سفارشات"])
                                </div>
                      </div>
                        <br/>
                        <br/>
                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary"> مشاهده گزارش</button>
                        <input type="hidden" id="leading_false" value="1">
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
@endsection


@section("scripts")
    @include("component.script_function.get_new_option")
    @include("component.input.datepicker.jalali_datepicker._script")
    <script>
        $("#goods_kind_id").change(function () {

            get_new_option(
                0,
                $("#goods_kind_id").val(),
                " طبقه بندی کالایی",
                "classification_id",
                "goods_kind_classification_group_option",
            )
        })


        $('#form1').validate({
            rules: {
                "start_date_value": "required",
                "end_date_value": "required",
                "classification_id": "required",
                "goods_kind_id": "required",
            }
        });
    </script>
@endsection
