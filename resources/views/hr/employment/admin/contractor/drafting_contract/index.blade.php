@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    @include('component.input.datepicker.jalali_datepicker._style')
    <form id="form1" style="display: inline"
          action="{{route("hr.employment.admin.contractor.drafting_contract.submit",$employment)}}"
          method="post"
          novalidate="novalidate" autocomplete="off"  enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-12">

                    <div class="card" >
                        <div class="card-header">
                            <h5>تنظیمات پیمانکار</h5>
                        </div>
                        <div class="card-block overflow-auto">

                            @include("hr.employment.admin.contractor.drafting_contract._info_setting")
                        </div>
                    </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                @if(!$data_contractor_default_setting->get_packing_form_details->enable || !$data_contractor_default_setting->input_form_loading_require->enable
                       ||!$data_contractor_default_setting->input_form_guarding_require_permission->enable||
                       !$data_contractor_default_setting->input_form_quality_control_permission->enable|| !$data_contractor_default_setting->checking_form_not_delivered_at_register_production->enable)

                    <div class="card" style="min-height: 700px">
                        <div class="card-header">
                            <h5>تنظیمات ثبت اطلاعات تولید</h5>
                        </div>
                        <div class="card-block overflow-auto">

                            @include("hr.employment.admin.contractor.drafting_contract._input_form")
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                @if(!$data_contractor_default_setting->checking_carrier_at_delivery_of_product->enable || !$data_contractor_default_setting->exit_form_require_draft_permission->enable
                  ||!$data_contractor_default_setting->exit_form_require_permission->enable||!$data_contractor_default_setting->exit_form_loading_require_permission->enable ||
                  !$data_contractor_default_setting->exit_form_guarding_require_permission->enable )

                    <div class="card"  style="min-height: 700px">
                        <div class="card-header">
                            <h5>ثبت اطلاعات برگ خروج </h5>
                        </div>
                        <div class="card-block overflow-auto">


                            <div class="row">
                                @include("hr.employment.admin.contractor.drafting_contract._exit_form")
                            </div>
                        </div>
                    </div>

                @endif

            </div>


            <div class="col-md-12" style="text-align: center">
                <a href="{{route("hr.employment.admin.dashboard.view",$employment)}}"
                   class="btn btn-outline-dark btn-lg">بازگشت</a>

                <button type="submit" class="btn btn-primary btn-lg"> ذخیره تغییرات</button>
            </div>
        </div>

    </form>
    @include('component.input.datepicker.jalali_datepicker._script')
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
                contractor_type_id_auto: "required",
                minimum_time_required_to_start_coordination:"required",
                end_date_of_contract_value: "required",
            }
        });

    </script>
@endsection
