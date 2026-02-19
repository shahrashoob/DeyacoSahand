@extends('layouts.admin._master')
@section("page_header_title","داشبورد ".($machine_allocation->machine?"ماشین آلات ":"پیمانکاران")."-  ".
($machine_allocation->machine?$machine_allocation->machine->fullCaption():$machine_allocation->contractor->caption)
)

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> {{$machine_allocation->machine?"کارت تولید ":"دستور پیمان"}} {{$machine_allocation->production->serial()}}
                        - تکمیل اطلاعات بسته بندی
                        {{$packing_form->code}}
                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action=" {{route("production.public_module.register_production.submit_complete_information",[$machine_allocation,$machine_allocation_packing_form,$source_production_form_item_id])}}"
                          method="post">
                        @csrf

                        @if(!isset($lot_number_property_1))
                            @include("component.input._number",["id"=>"lot_number_property_1","lable"=>"کیلوگرم بر متر کالا برای لات ".$packing_form_item->lot_number->code,"value"=>"","class_col"=>"col-md-4"])

                        @endif
                        @include("goods_kind_process.fabric_raw.packing_form.complete_information._info")


                        <a href="{{route("production.public_module.register_production.index",$machine_allocation)}}"
                           class="btn btn-outline-dark" style="width: 100px">بازگشت</a>
                        <button type="submit" class="btn btn-primary" style="width: 100px">تایید</button>


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
                "packing_type_id_auto": "required",
                "degree_id_auto": "required",
                "amount": {
                    required: true,
                    min: 1
                },
                "carrier_code": "required"
            }
        });
    </script>
@endsection
