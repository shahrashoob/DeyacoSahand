<script>
    @if(isset($smart_object))
        result_response = true;
    setInterval(getValueFromSmartObject, 1000);

    // اگر تابع تعریف نشده باشد، ان را ایجاد می کنیم.

    // هر جا استفاده کردیم، این تابع را باید از قبل تعریف کرده باشیم.
    // function action_after_get_response(jsonObj) {
    //     if (typeof jsonObj["weight"] !== 'undefined') {
    //         alert(jsonObj)
    //     } else {
    //         alert(jsonObj["error"])
    //     }
    // }

    function getValueFromSmartObject() {
        if (result_response) {

            result_response = false;

            host = '{{$smart_object->ip}}';
            port1 = '{{$smart_object->port}}'
            smart_id = '{{$smart_object->id}}'
            python_host = '{{$smart_object->get_server_ip()}}'
            python_port = '{{$smart_object->get_server_port()}}'
            request = $.ajax({
                url: "{{url("")}}/proxy/smart_object.php",
                type: "post",
                data: {
                    "python_host": python_host,
                    "python_port": python_port,
                    "host": host,
                    "port": port1,
                    "smart_id": smart_id,
                }
            });
            request.done(function (response, textStatus, jqXHR) {
                lock_submit = false;
                console.log(response);
                var jsonObj = JSON.parse(response);

                action_after_get_response(jsonObj);

                result_response = true;
            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                lock_submit = false;
                // Log the error to the console
                // alert("اطلاعات ثبت نشد، لطفا دوباره تلاش کنید.")
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
                result_response = true;
            });


        }
    }


@endif
</script>
