
<script>
    $("{{$btn_id}}").click(function () {

        if ($(this).hasClass("disabled")) {
            return false;
        }
        $(this).addClass("disabled");
        $("#button_list .btn").addClass("invisible disabled");
        $("#button_list .loading").removeClass("invisible");
        return 1;
    });
</script>
