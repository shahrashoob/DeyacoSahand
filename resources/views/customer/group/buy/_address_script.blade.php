<script>
    function setReadonly() {
        readonly = !$("#new_address").is(":checked");
        $("#country_id_auto").prop("readonly", readonly);
        $("#province_id_auto").prop("readonly", readonly);
        $("#city_name").prop("readonly", readonly);
        $("#phone").prop("readonly", readonly);
        $("#mobile").prop("readonly", readonly);
        $("#postal_code").prop("readonly", readonly);
        $("#address").prop("readonly", readonly);

        $("#country_id_auto").prop("required", !readonly ? "required" : "");
        $("#province_id_auto").prop("required", !readonly ? "required" : "");
        $("#city_name").prop("required", !readonly ? "required" : "");
        $("#phone").prop("required", !readonly ? "required" : "");
        $("#mobile").prop("required", !readonly ? "required" : "");
        $("#postal_code").prop("required", !readonly ? "required" : "");
        $("#address").prop("required", !readonly ? "required" : "");

    }

    $(".custom-control-input").click(function () {
        setReadonly();

    })
    setReadonly();
</script>