@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    @include('component.input.datepicker.jalali_datepicker._style')
    <form id="form1" style="display: inline"
          action="{{route("hr.employment.admin.supplier.drafting_contract.submit",$employment)}}"
          method="post"
          novalidate="novalidate" autocomplete="off" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header">
                        <h5>ثبت اطلاعات مالی</h5>
                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">
                            @include("hr.employment.admin.supplier.drafting_contract._financial_info")
                        </div>
                    </div>
                </div>
                @if(!$data_supplier_default_setting->get_packing_form_details->enable || !$data_supplier_default_setting->input_form_loading_require->enable
                ||!$data_supplier_default_setting->input_form_guarding_require_permission->enable||!$data_supplier_default_setting->input_form_quality_control_permission->enable)

                    <div class="card">
                        <div class="card-header">
                            <h5>ثبت اطلاعات فرم ورود</h5>
                        </div>
                        <div class="card-block overflow-auto">


                            <div class="row">
                                @include("hr.employment.admin.supplier.drafting_contract._input_form")
                            </div>
                        </div>
                    </div>
                @endif
                @if(!$data_supplier_default_setting->exit_form_require_quality_permission->enable || !$data_supplier_default_setting->exit_form_require_draft_permission->enable
                       ||!$data_supplier_default_setting->exit_form_require_permission->enable||!$data_supplier_default_setting->exit_form_loading_require_permission->enable ||
                       !$data_supplier_default_setting->exit_form_guarding_require_permission->enable )

                    <div class="card">
                        <div class="card-header">
                            <h5>ثبت اطلاعات برگ خروج </h5>
                        </div>
                        <div class="card-block overflow-auto">


                            <div class="row">
                                @include("hr.employment.admin.supplier.drafting_contract._exit_form")
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
                supplier_type_id_auto: "required",
                end_date_of_contract_value: "required",
            }
        });

    </script>
@endsection
