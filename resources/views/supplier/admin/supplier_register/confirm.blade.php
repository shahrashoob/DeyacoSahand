@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  مدیریت تامین کنندگان  ")

@section('content')

    <form id="form1" autocomplete="off" action="{{route("supplier.admin.supplier_register.submit_confirm")}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">

            <div class="col-md-12" id="card-block">

                <div class="card">
                    <div class="card-header">
                        <h5> فرم ثبت تامین ویژه دوره پیاده سازی </h5>
                    </div>
                    <div class="card-block" id="card-block">

                        <div class="row">
                            @include("component.input._lable",["label"=>"تامین کننده","value"=>$supplier->caption,"class_col"=>"col-md-3"])

                            <div class="w-100"></div> @include("component.input._lable",["label"=>"نوع","value"=>$debt_or_supply_id==1?"خرید":($debt_or_supply_id==2?"قرض":1/0),"class_col"=>"col-md-3"])

                            <div class="w-100"></div>

                            @include("component.input._lable",["label"=>" نام کالا","value"=>$product->fullCaption(),"class_col"=>"col-md-3"])

                            <div class="w-100"></div>

                            @include("component.input._lable",["label"=>"نوع انبارش کالا","value"=>$warehouse_storage_type->caption,"class_col"=>"col-md-3"])

                            <div class="w-100"></div>

                            @if($warehouse_storage_type->id == 2)

                                @include("component.input._lable",["label"=>"نوع بسته بندی","value"=>$packing_type->caption,"class_col"=>"col-md-3"])

                                <div class="w-100"></div>

                            @endif

                            @include("component.input._lable",["label"=>"درجه","value"=>$degree->caption,"class_col"=>"col-md-3"])

                            <div class="w-100"></div>

                            @include("component.input._lable",["label"=>"لات","id"=>"lot_number_code","value"=>$lot_number_code,"class_col"=>"col-md-3"])
                            <div class="w-100"></div>
                            @include("component.input._lable",["label"=>"مقدار کل","id"=>"amount","value"=>$amount." ".$product->unit->caption,"class_col"=>"col-md-3"])
                            <div class="w-100"></div>
                            @if($product->sub_unit)
                                @include("component.input._lable",["label"=>"مقدار فرعی","id"=>"sub_amount","value"=>$sub_amount." ".$product->sub_unit->caption,"class_col"=>"col-md-3"])
                                <div class="w-100"></div>
                            @endif
                            @if($warehouse_storage_type->id == 2)
                                @include("component.input._lable",["label"=>"تعداد بسته بندی","id"=>"packing_form_number","value"=>$packing_form_number,"class_col"=>"col-md-3"])
                                <div class="w-100"></div>
                            @endif
                            @if(!$sale_info_complete_later)

                                @include("component.input._lable",["label"=>"مبلغ کل بدون ارزش افزوده ","id"=>"price","value"=>number_format($price). " ریال","class_col"=>"col-md-3"])
                                <div class="w-100"></div>
                                @include("component.input._lable",["label"=>"مبلغ ارزش افزوده","id"=>"tax_price","value"=>number_format($tax_price)." ریال","class_col"=>"col-md-3"])
                            @else
                                <div class="col-md-12 text-warning ">
                                    <label style="font-weight: bold">اطلاعات خرید را بعدا تکمیل می کنم.</label>
                                </div>
                            @endif
                            <div class="w-100"></div>
                        </div>


                    </div>
                </div>
            </div>
            @if($supplier->input_form_loading_require)
                <div class="col-md-12">
                    <h5> اطلاعات بارگیری</h5>
                </div>
                @include("utility.transport.public._create_transport_view")
            @endif

            <div class="col-md-12 center">
                <a href="{{route("supplier.admin.supplier_register.index")}}"
                   class="btn btn-outline-dark">بازگشت</a>
                <button type="submit" class="btn btn-success"> تایید نهایی تامین</button>
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
                "product_id_auto": "required",
            }
        });
    </script>
@endsection
