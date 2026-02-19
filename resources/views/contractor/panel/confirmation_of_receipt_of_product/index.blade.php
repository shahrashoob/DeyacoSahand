@extends('layouts.admin._master')
@section("page_header_title","داشبورد پیمانکاران -  ".$contractor->fullCaption())

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>تایید دریافت کالا</h5>
                </div>
                <div class="card-block">

                    <form id="form1" autocomplete="off"
                          action="{{route("contractor.panel.confirmation_of_receipt_of_product.submit",[$contractor_allocation,$product_request_form_form,$form])}}"
                          method="post"
                          novalidate="novalidate">
                        @csrf


                            @include("utility.form._standard_conform_list",[
                                "back_route"=>route("contractor.panel.dashboard.view",[$contractor_allocation]),
                                "allow_show_packing_form_code"=>!$contractor_allocation->contractor->checking_carrier_at_delivery_of_product
                               ])


                    </form>
                </div>
            </div>
        </div>


    </div>

@endsection
@section("styles")

    @include("component.input.datepicker._script")
    <style>
        .form-group {
            margin: 0px !important;
        }

        .form-control {
            width: 150px !important;
            margin: auto;
        }
    </style>
@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                @php $row=1;@endphp
                    @foreach($product_request_form_form->form->item as $form_item)
                "packing_{{$row++}}": "required",
                @endforeach
            }
        });
    </script>
@endsection
