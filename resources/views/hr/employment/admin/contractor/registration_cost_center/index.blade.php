@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت اطلاعات مالی</h5>
                </div>
                <div class="card-block">

                    <form id="form1" style="display: inline"
                          action="{{route("hr.employment.admin.contractor.registration_cost_center.submit",$employment)}}"
                          method="post"
                          novalidate="novalidate" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @if($company_have_separate_warehousing_software)
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"cost_center_id",
                                        "label"=>"مرکز هزینه ",
                                        "option"=>$cost_center_option["items"],
                                        "val"=>$cost_center_option["value"],
                                        "text"=>$cost_center_option["text"],
                                        "class_col"=>"",
                                         "url"=>route("hr.employment.admin.contractor.cost_center.create",$employment),
                                        "url_text"=>" <i class='fa fa-plus'></i> "." "." مرکز هزینه جدید ",
                                        ])
                                @endif
                                @if($company_have_separate_financial_software)
                                    @include("component.input._text", ["id"=>"detailed_code", 'label'=>"کد تفضیلی",  "class_col"=>""])
                                @endif

                            </div>

                        </div>
                        <div class="w-100"><br/></div>

                        <a href="{{route("hr.employment.admin.dashboard.view",$employment)}}"
                           class="btn btn-outline-dark btn-lg">بازگشت</a>
                        <button type="submit" class="btn btn-primary">ذخیره</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    {{--    @include("component.input.datepicker._script")--}}
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                cost_center_id_auto: "required",
                detailed_code: "required",
            }
        });
    </script>
@endsection
