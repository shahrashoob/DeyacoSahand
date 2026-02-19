@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")
    <form id="form1" action="{{route($route_path."submit",[$product_creation_process])}}" method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>صدور دستور تولید نمونه اولیه کالا</h5>
                    </div>
                    <div class="card-block">
                        <div class="row">
                           @include("line_product_station.product.product_creation.sample_production_order._info")
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">صدور کارت نمونه گیری</button>
                            <a class="btn btn-outline-dark"
                               href="{{route($dashboard_path."view",$product_creation_process)}}">بازگشت</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        </div>
    </form>
@endsection
@section("styles")
    @include("component.input.datepicker._script")
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "amount": {
                    required: true,
                    max: {{$product_creation_process->product->goods_kind->max_number_for_sampling_production_card}}
                },
                "packing_type_id": "required",
                "max_delivery_datetime_value": "required"
            }
        });
    </script>
@endsection
