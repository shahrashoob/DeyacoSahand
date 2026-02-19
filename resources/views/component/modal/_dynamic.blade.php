<div id="exampleModalLong" class="modal fade modal  " tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{$title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body" id="modal_body_{{$id}}">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>

            </div>
        </div>
    </div>
</div>

<script>
    function loadModalContent(modalId) {
        $("#modal_body_{{$id}}").html( "<p>در حال بارگذاری</p>");
        equest = $.ajax({
            url: "{{url($url)}}",
            type: "post",
            headers: {
                "Authorization": "Bearer {{$token_api}}"
            },
            data: {
                "order_id": modalId,
            },
            success: function(data) {
                $("#modal_body_{{$id}}").html(data ?? "<p>هیچ محتوایی یافت نشد</p>");
            },
            error: function(xhr, status, error) {
                console.error(error);
                $("#modal_body_{{$id}}").html("<p class='text-danger'>خطا در دریافت محتوا</p>");
            }
        });


    }
    $(".order-modal").click(function () {

        loadModalContent($(this).data("id"))

    })


</script>