<script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
@include("component.script_function.get_new_option")
<script>

    $("#enter_weight,#enter_gross_weight,#enter_unit_amount").change(function () {
        update_entry_input();
    })

    function update_entry_input() {
        $(".enter_weight").css("display", $("#enter_weight").is(":checked") ? "" : "none");
        $(".enter_gross_weight").css("display", $("#enter_gross_weight").is(":checked") ? "" : "none");
        $(".enter_unit_amount").css("display", $("#enter_unit_amount").is(":checked") ? "" : "none");
    }

    update_entry_input();

</script>
@include("component._spinner",["id"=>".add_packing_row"])
