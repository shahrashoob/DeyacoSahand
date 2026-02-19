@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش  ماشین {{$machine->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.machine.update",$machine)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"code",'label'=>"کد ماشین","value"=>$machine->getCode(),"readonly"=>1])
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان ماشین ","value"=>$machine->caption,"autofocus"=>1])
                            @include("component.input._number",["id"=>"number_code",'label'=>"شماره ماشین ","value"=>$machine->number_code,"autofocus"=>1])
                            @include("component.input._number",["id"=>"next_relation_machine_code",'label'=>"شماره ماشین متناظر در ایستگاه بعدی","value"=>$machine->next_relation_machine_code,"autofocus"=>1])


                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"active_status_id",
                                    "label"=>" وضعیت   ",
                                    "option"=>$status_option["items"],
                                    "val"=>$machine->active_status->id??"",
                                    "text"=>$machine->active_status->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>



                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"check_inventory_for_allocation",'label'=>"موجودی مواد اولیه برای انبارک چک شود؟","checked"=>$machine->check_inventory_for_allocation??0])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"checking_form_not_delivered_at_register_production",'label'=>"آیا وجود فرم ورود به انبار تحویل نشده در زمان ثبت تولید توسط این ماشین چک شود؟ ","checked"=>$machine->checking_form_not_delivered_at_register_production??0])
                            </div>

                        </div>

                        <a href="{{route("line_product_station.machine.index",$machine->machine_type_id)}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary">  ذخیره تغییرات </button>

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
                "caption": "required",
                "code": "required",
                "status_id_auto": "required",
                "line_id_auto": "required",
                "number_code": "required",
                "raw_material_request_algorithm_type_id_auto": "required",
                "raw_material_request_sampling_algorithm_type_id_auto": "required"
            }
        });
    </script>
@endsection
