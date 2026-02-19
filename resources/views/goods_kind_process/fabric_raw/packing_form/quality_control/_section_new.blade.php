<div class="col-md-12">
    <div class="center" style="font-size: 25px; font-weight: bold; ">
        {{$qc_data["unit_caption"]["measurement"]}}
        <br/>

        <input
                type="number"
                class="qc_final_amount"
                style="width: 80px;
                text-align: center"
                id="qc_final_amount"
               value="{{$qc_data["last_amount_control"][1]}}"
               @if(isset($smart_object)) disabled @endif
        >

        <br/>
        <br/>

    </div>
    @if(isset($error_message))
        <div class="center alert alert-danger">
            {!! $error_message !!}
        </div>
        <script>
            var snd = new Audio("{{asset("assets/voice/alarm.mp3")}}");
            snd.play();
        </script>
    @endif
    @if(isset($success_message))
        <div class="center alert alert-success">
            {{$success_message}}
        </div>
    @endif
</div>
@if(count($qc_data["items"])==1)
    <div class="col-md-3">
        <br/>
    </div>
@endif
@foreach($qc_data["items"] as $band_code=>$band_data)
    @include("goods_kind_process.fabric_raw.packing_form.quality_control._band_view",["band_code"=>$band_code,"band_data"=>$band_data])

@endforeach

<div class="col-md-12 center">
    <a href="{{route("fabric_raw.packing_form.view",$packing_form)}}"
       class="btn btn-outline-dark" style="width: 200px">بازگشت</a>

    @php $end_of_qc=true;
        foreach($qc_data["is_end_of_quality_control"] as $band_code=>$value){
            if($value==false){
                $end_of_qc=false;
            }
        }
    @endphp
    @if($end_of_qc)
        <a href="{{route("fabric_raw.packing_form.quality_control.end_of_qc",$packing_form)}}"
           class="btn btn-primary" style="width: 200px">پایان کنترل کیفیت</a>

    @endif
    <a href="{{route("fabric_raw.packing_form.quality_control.reset",$packing_form)}}"
       class="btn btn-danger" style="width: 200px" onclick="return confirm('آیا از حذف اطلاعات کنترل کیفیت اطمینان دارید؟')">حذف  و بررسی مجدد</a>
</div>

<script>

    $(".btn_fault_point").click(function () {
        add_fault(
            "add_point_fault",
            $(this).data('id'),
            $(this).data('product_fault_type_id'),
            $(this).data('band_code')
        )
    })

    $(".btn_fault_start_point").click(function () {
        add_fault(
            "add_start_point_fault",
            $(this).data('id'),
            $(this).data('product_fault_type_id'),
            $(this).data('band_code')
        )
    })

    $(".btn_fault_end_point").click(function () {
        add_fault(
            "add_end_point_fault",
            $(this).data('id'),
            $(this).data('product_fault_type_id'),
            $(this).data('band_code')
        )
    })

    $(".btn_end_item").click(function () {
        add_fault(
            "btn_end_item",
            "",
            "",
            $(this).data('band_code')
        )
    })

    $(".btn_end_item_new_packing").click(function () {
        add_fault(
            "btn_end_item_new_packing",
            "",
            "",
            $(this).data('band_code')
        )
    })

    $(".btn_end_new_packing").click(function () {
        add_fault(
            "btn_end_new_packing",
            "",
            "",
            $(this).data('band_code')
        )
    })

    $(".btn_end").click(function () {
        add_fault(
            "btn_end",
            "",
            "",
            $(this).data('band_code')
        )
    })



    function add_fault(action_type,id, product_fault_type_id, band_code,property_values,product_fault_fixed_type_id) {


        if ($("#qc_final_amount").val() == "" || parseInt($("#qc_final_amount").val()) < 0) {
            alert("لطفا مقدار کنترل کیفیت را وارد نمایید.")
            return 0;
        }

        var qc_final_amount = $("#qc_final_amount").val();
        spinner_run_for()

        request = $.ajax({
            url: "{{url("api/packing_form/quality_control/submit_change_api")}}",
            type: "post",
            data: {
                "packing_form_id": {{$packing_form->id}},
                "action_type": action_type,
                "product_fault_id": id,
                "product_fault_type_id": product_fault_type_id,
                "band_code": band_code,
                "qc_final_amount": qc_final_amount,
                "property_values":JSON.stringify(property_values),
                "product_fault_fixed_type_id":product_fault_fixed_type_id,
            }
        });
        request.done(function (response, textStatus, jqXHR) {

            $("#qc_content").html(response);
        });
        request.fail(function (jqXHR, textStatus, errorThrown) {
            // Log the error to the console
            alert("اطلاعات ثبت نشد، لطفا دوباره تلاش کنید.")
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
    }

    function changeValue(delta) {
        const input = document.getElementById('qc_final_amount');
        input.value = parseInt(input.value || 0) + delta;
    }

    function action_after_get_response(jsonObj) {
        alert("این ماژول هنوز پیاده سازی نشده است، لطفا با واحد پشتیبانی تماس بگیرید.")
        // if (typeof jsonObj["weight"] !== 'undefined') {
        //     $value = parseFloat(jsonObj["weight"]);
        //     if (id_gross_weight != "" && $value >= 0) {
        //         $("#" + id_gross_weight).val($value);
        //         set_id_for_smart_object("");
        //
        //
        //     }
        //     setTimeout(set_id_for_smart_object, 2000, id_gross_weight_after_time_out)
        // } else {
        //     alert(jsonObj["error"])
        // }
    }



</script>

@include("component.smart_object._get_value_from_smart_object")


{{--    <div class="col-md-6">--}}
{{--        <div class="card">--}}
{{--            <div class="card-header"><h5 class="card-title">باند 2</h5></div>--}}
{{--            <div class="collapse show">--}}
{{--                <div class="card-body">--}}
{{--                    <div class="center" style="font-size: 16px">--}}
{{--                        متراژ:--}}
{{--                        <input type="number">--}}
{{--                        <br/>--}}
{{--                        <br/>--}}
{{--                    </div>--}}
{{--                    <button type="button" class="btn btn-outline-primary">Primary</button>--}}
{{--                    <button type="button" class="btn btn-outline-secondary">Secondary</button>--}}
{{--                    <button type="button" class="btn btn-outline-success">Success</button>--}}
{{--                    <button type="button" class="btn btn-outline-danger">Danger</button>--}}
{{--                    <button type="button" class="btn btn-outline-warning">Warning</button>--}}
{{--                    <button type="button" class="btn btn-outline-info">Info</button>--}}
{{--                    <button type="button" class="btn btn-outline-light">Light</button>--}}
{{--                    <button type="button" class="btn btn-outline-dark">Dark</button>--}}
{{--                    <br/>--}}

{{--                    <div class="center">--}}
{{--                        <br/>--}}
{{--                        <br/>--}}

{{--                        <button type="button" class="btn btn-primary">پایان</button>--}}
{{--                        <button type="button" class="btn btn-primary">پایان و بسته بندی جدید</button>--}}
{{--                        <button type="button" class="btn btn-primary">پایان آیتم</button>--}}
{{--                        <button type="button" class="btn btn-primary">پایان آیتم و بسته بندی جدید</button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--        </div>--}}

{{--    </div>--}}

