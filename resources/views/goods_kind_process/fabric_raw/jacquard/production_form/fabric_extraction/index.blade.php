@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تولید بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> استخراج فرم {{$production_form->getCode()}} </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.jacquard.production_form.fabric_extraction.submit",$production_form)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        @include("goods_kind_process.fabric_raw.public._shift_and_counter",["machine"=>$production_form->machine])

                        <div class="w-100"></div>
                        <div class="col-md-6">
                            @include("component.input._aotocomplet2",[
                                "id"=>"fabric_raw_type_of_cut_id",
                                "label"=>" محل برش پارچه ",
                                "option"=>$type_of_cut_option["items"],
                                "val"=>$type_of_cut_option["value"],
                                "text"=>$type_of_cut_option["text"],
                                "class_col"=>""
                                ])
                        </div>

                        @if($allocation_item->number_of_doffs_done < $allocation_item->max_number_of_doffs-1 && $reserve_production_form_count == 0)
                            @include("component.input._text",["id"=>"carrier_id","label"=>"شماره غلطک جدید پارچه خام","value"=>$carrier_id??""])
                        @endif

                        @if(isset($carrier_id))
                            <div class="alert alert-danger">
                                با توجه به اینکه حامل
                                {{$carrier_id}}
                                بر روی همین ماشین
                                در حال تکمیل می باشد، آیا از انتخاب حامل اطمینان دارید؟
                                <br/>
                                (توجه داشته باشید که در صورت تایید، شما ملزم به تغییر بسته بندی به قید فوریت می باشید.)
                            </div>
                        @endif
                        <div class="col-md-12">
                            <a href="{{route("fabric_raw.production_form.view",$production_form)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success">تایید و ادامه</button>
                        </div>

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
                "shift_work_id_auto": "required",
                "contour_1_value": "required",
                "contour_2_value": "required",
                "contour_3_value": "required",
                "carrier_id": "required",
                "fabric_raw_type_of_cut_id_auto": "required",
            }
        });
    </script>
@endsection
