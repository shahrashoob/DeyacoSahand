<div class="alert alert-warning">
    @if(isset($lot_number))
        برای لات {{$lot_number_code}}
        مشخصات تعریف نشده است، لطفا مشخصات زیر را تکمیل نمایید.

    @else
        لات
        {{$lot_number_code}}
        برای کالا تعریف نشده است، آیا از تعریف لات جدید اطمینان دارید؟

    @endif

        <input type="hidden" id="lot_number_code" value="{{$lot_number_code}}">
    @php
        $get_property_1=$is_complete_information==-1;
         $get_property_1=$get_property_1 &&
         (
            $machine_allocation->product->unit_id == 1100 ||
            $machine_allocation->product->sub_unit_id == 1100
            )
            ;
    @endphp
    @if($get_property_1)
        {{--        این مشخصه برای کالاهایی است که واحد اصلی یا فرعی آنها متر(یا انواع واحد های متری) باشد--}}
            <br/>
            لطفا اطلاعات زیر را تکمیل نمایید:
            <br/>
            کیلوگرم بر متر کالا
            <input type="number" id="lot_number_property_1" value="{{$default_property_value[1]}}">
            <br/>
            <br/>
    @endif
    <a href="#" id="add_new_lot_number_to_product" class="btn btn-outline-primary btn-sm">
        @if(isset($lot_number))
            <i class="fa fa-pen"></i>
            ثبت مشخصات لات
        @else
            <i class="fa fa-plus"></i>
            تعریف لات جدید
        @endif
    </a>
</div>

<input type="hidden" id="add_new_lot" value="1">

<script>
    $("#add_new_lot_number_to_product").click(function () {
        add_new_lot_number();
    });

    function add_new_lot_number() {

        var lot_number_property_1 = $("#lot_number_property_1").val();

        var lot_number_code = $("#lot_number_code").val();

        @if($get_property_1)
        if (isNaN(parseFloat(lot_number_property_1))) {
            alert("لطفا مشخصه را به درستی وارد نمایید.")
            return 0;
        }
        @endif

            request = $.ajax({
            url: "{{url("api/production/public_module/add_new_packing/add_new_lot_number_to_product_api")}}",
            type: "post",
            data: {
                "machine_allocation_id": {{$machine_allocation->id}},
                "user_id": {{$user_id}},
                "lot_number_code": lot_number_code,
                "lot_number_property_1": lot_number_property_1,
                "is_complete_information": {{$is_complete_information}},
                "degree_id": {{$degree_id}},
                "smart_object_id": {{$smart_object->id??0}},
                "default_lot_number_id": {{$default_lot_number->id??0}},
                "source_production_form_item_id": {{$source_production_form_item_id??0}}
            }
        });
        request.done(function (response, textStatus, jqXHR) {

            $("#panel_packing_item").html(response);
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
</script>
