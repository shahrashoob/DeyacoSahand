@extends('layouts.admin._master')
@section('page_header_title',"داشبورد  تولید")
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning  ">
                <b>
                    با توجه به اینکه کالای
                    جدول زیر
                    به مدت
                    {{$warehouse_handling_time_limit}}
                    روز است که انبارگردانی نشده است، لطفا ابتدا این کالا را انبار گردانی
                    کنید.
                </b>
            </div>
        </div>


        @include("goods_kind_process.general.machine.material_return_to_warehouse._product_list",["allow_delete"=>false,"alert_class"=>"alert-warning"])

        <div class="col-md-12 center">
            <a href="{{route($dashboard_route."view",$machine)}}" class="btn btn-outline-dark">بازگشت</a>
            <a href="{{route($route_path."confirm",[$machine,$warehouse])}}" class="btn btn-primary">تایید
                نهایی</a>
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
@endsection @section("scripts")

    <script>

        $(".consumed_radio").change(function () {

            if ($(this).val() == 6021101 || $(this).val() == 6021103) {
                $("#input_packing_" + $(this).data("packing_form_id")
                ).css("display", "none");
            } else {
                $("#input_packing_" + $(this).data("packing_form_id")
                ).css("display", "");
            }
        })
        $('#form1').validate({
            rules: {
                "description": "required",
            }
        });

        $("#btn_submit").click(function () {
            set_id_for_smart_object("");
            // قبل از ارسال همه را غیر فعال میکنیم تا اطلاعات ارسال شود.
            gross_weight_input_status(false)
            setTimeout(gross_weight_input_status, 1000, true);
        })

    </script>
    @include("goods_kind_process.general.machine.material_return_to_warehouse._scale_script")
    @include("component.smart_object._get_value_from_smart_object")

@endsection
@section("scripts")


@endsection

