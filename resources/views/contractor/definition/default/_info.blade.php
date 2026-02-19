<div class="row">
    <div class="col-md-12">

        <div class="card">
            <div class="card-header">
                <h5>تنظیمات پیمانکاران</h5>
            </div>
            <div class="card-block overflow-auto">
                @include("utility.setting._contractor")
            </div>
        </div>
    </div>
</div>

<form id="form1" style="display: inline" action="{{route("contractor.definition.default.submit")}}"
      method="post"
      novalidate="novalidate" autocomplete="off">
    @csrf

    <div class="row">
        <div class="col-md-12">


            <div class="card">
                <div class="card-header">
                    <h5>تنظیمات پیش فرض برگ خروج</h5>
                </div>
                <div class="card-block overflow-auto">


                    <div class="row">
                        @include("contractor.definition.default._exit_form")
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>تنظیمات ثبت اطلاعات تولید</h5>
                </div>
                <div class="card-block overflow-auto">


                    <div class="row">
                        @include("contractor.definition.default._input_form")
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
        $('#contractor_draft_contract_confirm_1,#contractor_draft_contract_confirm_0').change(function () {
            contractor_draft_contract_confirm();

        });

        function contractor_draft_contract_confirm() {
            if ($('input:radio[name=contractor_draft_contract_confirm]:checked').val() == '1') {
                $('#contractor_draft_contract_required_init_confirm_1').parent().show();
                $('#contractor_draft_contract_post_ids_for_init_confirm').parent().show();
                $('#contractor_draft_contract_required_final_confirm_1').parent().show();
                $('#contractor_draft_contract_post_ids_for_final_confirm').parent().show();
            } else {
                $('#contractor_draft_contract_required_init_confirm_1').parent().hide();
                $('#contractor_draft_contract_post_ids_for_init_confirm').parent().hide();
                $('#contractor_draft_contract_required_final_confirm_1').parent().hide();
                $('#contractor_draft_contract_post_ids_for_final_confirm').parent().hide();
            }
        }

        contractor_draft_contract_confirm();
    </script>
@endsection