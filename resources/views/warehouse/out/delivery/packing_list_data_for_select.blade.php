@extends('layouts.admin._master',["keypress_enable"=>1])

@section('page_header_title',"داشبورد  تحویل انبار  ")

@section('content')
    <div class="row">
        <div class="col-sm-12" id="card">
            <div class="card">
                <div class="card-header">
                    <h5>
                        لیست همه بسته بندی های مجاز انتخاب برای
                        @if(isset($dashboard_type) && $dashboard_type=="customer")
                            {{$product_request_form->order->customer->caption}}
                        @else
                            درخواست
                            {{$product_request_form->getCode()}}
                        @endif

                    </h5>
                </div>

                    @include("warehouse.out.delivery._packing_list_data_for_select")


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
                "unit_id": "required",
                "sub_unit_id": "required",
                "carrier_code": "required",
                "lot_number": "required",
                "degree_id_auto": "required",
            }
        });





    </script>
@endsection


