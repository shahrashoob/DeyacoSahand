@extends('layouts.admin._master')

@section("page_header_title","داشبورد مدیریت کارت های تامین (کالای امانی)")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> کارت تامین (کالای امانی) {{$production->serial()}}</h5>
                </div>
                <div class="card-block">

                    @include("supplier.trust_product.dashboard._info_small")

                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5> انتخاب مقدار تخصیص</h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("supplier.trust_product.trust_product_allocation.submit",$production)}}"
                          method="post" novalidate="novalidate">
                        @csrf

                        <div class="row">
                            @include("component.input._number",["id"=>"allocation_amount","label"=>"مقدار تخصیص","value"=>$allocation_amount,"class_col"=>"col-md-3"])
                            <div class="w-100"><br/></div>
                            @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["hasTime"=>"","id"=>"predict_of_production_start_date_practical","lable"=>"تاریخ و زمان ارسال کالا ","class_col"=>"col-md-3","min_date"=>$min_date,"value"=>""])

                            <div class="w-100"><br/></div>
                            <div class="col-md-12">
                                <a href="{{route("supplier.trust_product.dashboard.view_card",$production)}}"
                                   class="btn btn-outline-dark">بارگشت </a>
                                <button type="submit" class="btn btn-primary">ثبت تخصیص</button>
                            </div>
                        </div>



                    </form>

                </div>
            </div>
        </div>

        @include("production.public.log_status")

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker.jalali_datepicker._style")
@endsection
@section("scripts")

    @include("component.input.datepicker.jalali_datepicker._script")
    <script>
        $('#form1').validate({
            rules: {
                allocation_amount: {required: true, min: 0, max:{{$allocation_amount}}},
                line_product_station_id: {required: true},
                predict_of_production_start_date_practical_value:{required: true},
            }
        });
    </script>
@endsection
