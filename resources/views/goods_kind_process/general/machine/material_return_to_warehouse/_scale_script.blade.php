<script>
    var id_gross_weight = "";

    function set_id_for_smart_object(id){
        id_gross_weight=id;
        $(".smart_object_icon ").removeClass("text-success");
        $("#"+id+"_smart_object").addClass("text-success");
    }

    @if(isset($smart_object))
        gross_weight_input_status(true)
    @endif


    function action_after_get_response(jsonObj) {
        if (typeof jsonObj["weight"] !== 'undefined') {
            $value = parseFloat(jsonObj["weight"]);
            if (id_gross_weight != "" && $value > 0 ) {
                $("#" + id_gross_weight).val($value);
                set_id_for_smart_object("")

            }

        } else {
            alert(jsonObj["error"])
        }
    }
</script>
