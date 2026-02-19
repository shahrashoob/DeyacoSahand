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


                    @php $final_shrinkage_percent=$packing_form->final_shrinkage_percent(); @endphp
                    @include("goods_kind_process.fabric_raw.packing_form._info_small",["final_shrinkage_percent"=>$final_shrinkage_percent])


                    @include("goods_kind_process.fabric_raw.packing_form._action",["back"=>"dashboard"])


                </div>
            </div>
        </div>

        {{--        @include("goods_kind_process.fabric_raw.production_card.public.log_status")--}}

        @include("goods_kind_process.fabric_raw.packing_form._item_list")
        {{--        @include("goods_kind_process.public.production_log_status");--}}
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
