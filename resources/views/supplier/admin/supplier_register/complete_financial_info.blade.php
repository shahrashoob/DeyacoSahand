@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  مدیریت تامین کنندگان  ")

@section('content')

    <form id="form1" autocomplete="off"
          action="{{route("supplier.admin.supplier_register.submit_complete_financial_info",$form_general_item)}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">

            <div class="col-md-12" id="card-block">

                <div class="card">
                    <div class="card-header">
                        <h5> ثبت اطلاعات مالی برای تخصیص {{$allocation->id}}  </h5>
                    </div>
                    <div class="card-block" id="card-block">

                        <div class="row">


                            @include("component.input._lable",["label"=>"کالا","id"=>"amount","value"=>$form_general_item->product->caption,"class_col"=>"col-md-12"])
                            <div class="w-100"></div>
                            @if($form_general_item->warehouse_storage_type && $form_general_item->warehouse_storage_type->id == 2)
                                @include("component.input._lable",["label"=>"نوع بسته بندی","id"=>"amount","value"=>$form_general_item->packing_type->caption,"class_col"=>"col-md-12"])
                                <div class="w-100"></div>
                            @endif
                            @include("component.input._lable",["label"=>"مقدار کل","id"=>"amount","value"=>$form_general_item->amount." ".$form_general_item->product->unit->caption,"class_col"=>"col-md-12"])
                            <div class="w-100"></div>

                            @include("component.input._lable",["label"=>"نوع انبارش کالا","value"=>$form_general_item->warehouse_storage_type->caption,"class_col"=>"col-md-3"])

                            <div class="w-100"></div>
                            @if($form_general_item->product->sub_unit)
                                @include("component.input._lable",["label"=>"مقدار فرعی","id"=>"sub_amount","value"=>$form_general_item->sub_amount." ".$form_general_item->product->sub_unit->caption,"class_col"=>"col-md-12"])
                                <div class="w-100"></div>
                            @endif

                            @if($form_general_item->warehouse_storage_type->id == 2)
                                <div class="w-100"></div>
                                @include("component.input._lable",["label"=>"تعداد بسته بندی","id"=>"packing_form_number","value"=>$form_general_item->packing_form_number,"class_col"=>"col-md-3"])
                            @endif

                            <div class="w-100"></div>
                            @include("component.input._number",["label"=>"مبلغ کل بدون ارزش افزوده (ریال) ","id"=>"price","value"=>"","class_col"=>"col-md-3"])
                            <div class="w-100"></div>
                            @include("component.input._number",["label"=>"مبلغ ارزش افزوده (ریال)","id"=>"tax_price","value"=>"","class_col"=>"col-md-3"])


                        </div>


                    </div>
                </div>

                <div class="col-md-12 center">
                    <button type="submit" class="btn btn-primary"> ثبت اطلاعات مالی</button>

                </div>
            </div>


        </div>
    </form>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

@endsection

@section("scripts")
    @include("component.script_function.get_new_option")
    <script>

        $('#form1').validate({
            rules: {
                "price": "required",
                "tax_price": "required",
            }
        });

    </script>
@endsection
