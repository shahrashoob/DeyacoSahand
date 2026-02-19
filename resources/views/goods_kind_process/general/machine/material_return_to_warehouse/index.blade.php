@extends('layouts.admin._master') @section('page_header_title',"داشبورد  تولید")
@section('content')
    <div class="row">


        @include("goods_kind_process.general.machine.material_return_to_warehouse._product_list")


        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5> مواد اولیه تغییر درجه داده شده</h5></div>
                <div class="card-block" style="overflow: auto">

                    <div class="center">
                        آیا هیچ کدام از مواد اولیه تغییر درجه داشته اند؟
                        <br/>
                        <input type="radio" value="1" name="change_of_grade_radio"  id="change_of_grade_yes"> بله
                        <input type="radio" value="-1" name="change_of_grade_radio" checked id="change_of_grade_no"> خیر
                        <br/>
                        <br/>

                    </div>
                    <div id="panel_change_grade" style="display: none">
                        @include("goods_kind_process.general.machine.material_return_to_warehouse._add_change_grade")
                    </div>

                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5>مواد اولیه ضایعات شده</h5></div>
                <div class="card-block" style="overflow: auto">

                    <div class="center">
                        آیا هیچ کدام از مواد اولیه ضایعات شده است؟
                        <br/>
                        <input type="radio" value="1" name="waste" id="waste_yes"> بله
                        <input type="radio" value="-1" name="waste" checked id="waste_no"> خیر
                        <br/>
                        <br/>

                    </div>
                    <div id="panel_waste" style="display: none">
                        @include("goods_kind_process.general.machine.material_return_to_warehouse._add_waste")
                    </div>


                </div>
            </div>
        </div>
<div class="col-md-12 center">
    <a href="{{route($dashboard_route."view",$machine)}}" class="btn btn-outline-dark">بازگشت</a>
    <a href="{{route($route_path."confirm",[$machine,$machine->warehouse_id])}}" class="btn btn-primary">تایید نهایی</a>
    <a href="{{route($route_path."reset_removed_product_from_list",[$machine])}}" class="btn btn-warning" onclick="return confirm('در صورت تایید، کلیه کالاهای حذف شده به لیست برگشت کالا بر می گردند.')" > <i class="fas fa-undo-alt"></i>  بازیابی کالاهای حذف شده</a>

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

    <script>
        $("#change_of_grade_yes,#change_of_grade_no").change(function () {
            if ($(this).val() == 1) {
                $("#panel_change_grade").css("display", "");
            } else {
                $("#panel_change_grade").css("display", "none");
            }

            $("#change_of_grade").val($(this).val())
        })
        $("#waste_no,#waste_yes").change(function () {
            if ($(this).val() == 1) {
                $("#panel_waste").css("display", "");
            } else {
                $("#panel_waste").css("display", "none");
            }

            $("#waste").val($(this).val())
        })
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
        })


        $("#btn_submit").click(function () {
            set_id_for_smart_object("");
            if ($("#change_of_grade").val() == 0) {
                alert("لطفا مشخص فرمایید آیا هیچ کدام از مواد اولیه تغییر درجه داشته اند یا خیر");
                return false;
            }
            // قبل از ارسال همه را غیر فعال میکنیم تا اطلاعات ارسال شود.
            gross_weight_input_status(false)
            setTimeout(gross_weight_input_status, 1000, true);
        })
    </script>

    {{--    باسکول--}}
    @include("goods_kind_process.general.machine.material_return_to_warehouse._scale_script")
    @include("component.smart_object._get_value_from_smart_object")

@endsection
@section("scripts")


@endsection

