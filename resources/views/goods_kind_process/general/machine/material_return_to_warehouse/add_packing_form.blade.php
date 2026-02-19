@extends('layouts.admin._master') @section('page_header_title',"داشبورد  تولید")
@section('content')
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5>ثبت اطلاعات مصرف بسته بندی ها برای {{$product->fullCaption()}}</h5></div>
                <div class="card-block" style="overflow: auto"  id="panel_add_packing_form">

                    @include("goods_kind_process.general.machine.material_return_to_warehouse._add_packing_forms")


                </div>
            </div>
        </div>


    </div>

@endsection

@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <script>
        function gross_weight_input_status(value) {
            $(".gross_weight_input input").prop('disabled', value);
        }
    </script>
@endsection
@section("scripts")



    {{--    باسکول--}}
    @include("goods_kind_process.general.machine.material_return_to_warehouse._scale_script")
    @include("component.smart_object._get_value_from_smart_object")

@endsection


