@extends('layouts.admin._master')

@section('page_header_title',"داشبورد بسته بندی  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم بسته بندی {{$packing_form->code}}</h5>
                </div>
                <div class="card-block">


                    @include("packing_form.dashboard._info_small")


                    @include("packing_form.dashboard._action")


                </div>
            </div>
        </div>

        {{--        @include("goods_kind_process.fabric_raw.production_card.public.log_status")--}}

        @include("packing_form.dashboard._item_list")
        @include("packing_form.dashboard._log");
    </div>

@endsection

@section("scripts")
    <script>
        $("#btn_quality_control").click(function () {
            return confirm("آیا از تایید کنترل کیفیت محصولات تولید شده اطمینان دارید؟");
        })
        $("#btn_delivery_to_warehouse").click(function () {
            return confirm("آیا از تحویل کالا به انبار تولید  اطمینان دارید؟");
        })

        function myFunction(code) {

            let mycode = prompt("لطفا شماره ردیف را وارد نمایید:", "");
            if (mycode == null || mycode == "") {
                return false;
            } else {
                if (mycode != code) {
                    alert("شماره ردیف وارد شده صحیح نمی باشد.")
                    return false;
                }
            }

        }
    </script>
@endsection
