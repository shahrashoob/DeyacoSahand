

<script>
    var x =@php echo json_encode($x_json); @endphp;


    request = $.ajax({
        url: "{{url("api/option/get")}}",
        type: "post",
        data: {
            "type": type,
            "id": id,
            "label": label,
            "select_id": select_id,
            "model": model,
            "list":$list
        }
    });
    request.done(function (response, textStatus, jqXHR) {

        $("#"+select_id).html(response);
    });
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // Log the error to the console
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
</script>
