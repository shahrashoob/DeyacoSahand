<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>تنظیمات مشتری</h5>
            </div>
            <div class="card-block overflow-auto">


                <div class="row">
                    @include("utility.setting._customer")
                </div>
            </div>
        </div>
        <form id="form1" style="display: inline" action="{{route("customer_group.definition.default.submit")}}"
              method="post"
              novalidate="novalidate" autocomplete="off">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h5>تایید های مورد نیاز مشتری</h5>
                </div>
                <div class="card-block overflow-auto">


                    <div class="row">
                        @include("customer.definition.default._order_permission_customer")
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>تنظیمات فرم ورود (تحویل امانی)</h5>
                </div>
                <div class="card-block overflow-auto">


                    <div class="row">
                        @include("customer.definition.default._input_form")
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>تنظیمات برگ خروج</h5>
                </div>
                <div class="card-block overflow-auto">


                    <div class="row">
                        @include("customer.definition.default._exit_form")
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>روش پرداخت </h5>
                </div>
                <div class="card-block overflow-auto">


                    <div class="row">
                        @include("customer.definition.default._payment_method_type")
                    </div>
                </div>
            </div>

    </div>


    <div class="col-md-12" style="text-align: center">
        <a href="{{route("dashboard")}}"
           class="btn btn-outline-dark btn-lg">بازگشت</a>

        <button type="submit" class="btn btn-primary btn-lg"> ذخیره تغییرات پیش فرض</button>
    </div>
</div>

</form>

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>

        $('#form1').validate({
            rules: {
                detailed_code: "required",
                economic_number2: "required",
                code: "required",
                cash_off_percent: "required",
                bail_amount: "required",

                confirm_password: {equalTo: "#password"},
                tariff_id_auto: "required",
                priority_id_auto: "required",

                the_max_day_allowed_to_conform_exit_form_to: {required: true, min: 1},

                percent_tax_off_in_formal_factor: {required: true, min: 0, max: 100},
                percent_max_informal_purchase: {required: true, min: 0, max: 100},
                address: "required",
                country: "required"
            }
        });
        $('#customer_draft_contract_confirm_1,#customer_draft_contract_confirm_0').change(function () {
            customer_draft_contract_confirm();

        });
        function customer_draft_contract_confirm() {
            if ($('input:radio[name=customer_draft_contract_confirm]:checked').val() == '1') {

                $('#customer_draft_contract_required_init_confirm_1').parent().show();
                $('#customer_draft_contract_post_ids_for_init_confirm').parent().show();
                $('#customer_draft_contract_required_final_confirm_1').parent().show();
                $('#customer_draft_contract_post_ids_for_final_confirm').parent().show();
            } else {
                $('#customer_draft_contract_required_init_confirm_1').parent().hide();
                $('#customer_draft_contract_post_ids_for_init_confirm').parent().hide();
                $('#customer_draft_contract_required_final_confirm_1').parent().hide();
                $('#customer_draft_contract_post_ids_for_final_confirm').parent().hide();
            }
        }
        customer_draft_contract_confirm();

        $(".payment_method").change(function () {

            if ($("#payment_method_type_" + $(this).data('id')).is(':checked')) {

                $(".payment_method_input_" + $(this).data('id')).css('display', "block");
            } else {

                $(".payment_method_input_" + $(this).data('id')).css('display', "none");
            }
        })
        $(".payment_method").change();

        setInterval(function () {
        }, 200)

        $("#customer_type_id").change(function () {

            if ($("#customer_type_id").val() == 1) {

                $("#customer_type1").css("display", "block");
                $("#customer_type2").css("display", "none");
            } else {
                $("#customer_type1").css("display", "none");
                $("#customer_type2").css("display", "block");
            }
        })
    </script>
@endsection