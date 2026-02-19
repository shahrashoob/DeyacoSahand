@extends('layouts.admin._master')

@section('page_header_title'," مجوزها ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> درخواست مجوز برای {{$special_license_type->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("utility.special_license.panel.new_special_license.store",[$special_license_type,$reference,$param1,$param2,$param3,$param4,$param5])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("utility.special_license.panel.new_special_license._info")
                            @include("utility.special_license.type_view.type".$special_license_type->id."._reference_info")
                            @include("component.input._textarea",["lable"=>"علت درخواست"." (".$special_license_type->caption.")","id"=>"description","value"=>$description,"class_col"=>"col-md-9","height"=>"80px"])

                        </div>

                        <a href="{{$special_license_type->getBackUrl($reference,$param1,$param2,$param3,$param4,$param5)}}"
                           class="btn btn-outline-dark">بازگشت </a>

                        <button type="submit" class="btn btn-success">تایید و ثبت درخواست</button>

                    </form>

                </div>
            </div>
        </div>

        @include("utility.special_license.panel.new_special_license._confirm_expert")


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
                "caption": "required",
                "status_id_auto": "required",
                "description": {required: true, minlength: 5},

                "end_datetime_value": "required",
                "end_time_h": "required",
                "start_datetime_value": "required",
                "start_time_h": "required",
                "packing_type_id_auto": "required",

                "supplier_id": "required",
                "supplier_id_auto": "required",
                'owner_personal_id_in_ic_auto': "required",
                "carrier_group_id_auto": "required",
                "unit_id_auto": "required",
                "min_band_number": "required",
                "max_band_number": "required",
                "min_band_capacity": "required",
                "max_band_capacity": "required",
                "length": "required",
                "width": "required",
                "height": "required",
                "weight": "required",
                "active_status_id_auto": "required",
                "carrier_type_id_auto": "required",
                "packing_type_label_printing_type_id_auto": "required",
                "label_caption": "required",
                "weight_error_percentage": "required",
                "printer_unit_display_type_id_auto": "required",
                "count_packing_layer_auto": "required",
                "discharge_type_id_auto": "required",
            }
        });
    </script>
@endsection

