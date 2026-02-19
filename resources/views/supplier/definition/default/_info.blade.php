<div class="row">
    <div class="col-md-12">

        <div class="card">
            <div class="card-header">
                <h5>تنظیمات تامین کنندگان</h5>
            </div>
            <div class="card-block overflow-auto">
                @include("utility.setting._supplier")
            </div>
        </div>
    </div>
</div>

<form id="form1" style="display: inline" action="{{route("supplier.definition.default.submit")}}"
      method="post"
      novalidate="novalidate" autocomplete="off">
    @csrf

    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <h5>تنظیمات پیش فرض فرم ورود</h5>
                </div>
                <div class="card-block overflow-auto">


                    <div class="row">
                        @include("supplier.definition.default._input_form")
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>تنظیمات پیش فرض برگ خروج (تحویل امانی)</h5>
                </div>
                <div class="card-block overflow-auto">


                    <div class="row">
                        @include("supplier.definition.default._exit_form")
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
        $('#supplier_draft_contract_confirm_1,#supplier_draft_contract_confirm_0').change(function () {
            supplier_draft_contract_confirm();

        });

        function supplier_draft_contract_confirm() {
            if ($('input:radio[name=supplier_draft_contract_confirm]:checked').val() == '1') {
                $('#supplier_draft_contract_required_init_confirm_1').parent().show();
                $('#supplier_draft_contract_post_ids_for_init_confirm').parent().show();
                $('#supplier_draft_contract_required_final_confirm_1').parent().show();
                $('#supplier_draft_contract_post_ids_for_final_confirm').parent().show();
            } else {
                $('#supplier_draft_contract_required_init_confirm_1').parent().hide();
                $('#supplier_draft_contract_post_ids_for_init_confirm').parent().hide();
                $('#supplier_draft_contract_required_final_confirm_1').parent().hide();
                $('#supplier_draft_contract_post_ids_for_final_confirm').parent().hide();
            }
        }

        supplier_draft_contract_confirm();
    </script>
@endsection