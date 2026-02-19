<script>
    packing_form_codes = {!! json_encode($packing_form_codes) !!};

    $('.input_packing_code').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            packing_code=$(this).val()
            if(packing_code in packing_form_codes ){
                focusNext()
            }
            else{
                if($(this).val()!="") {
                    alert("بسته بندی DCPK/" + $(this).val() + " جزء بسته بندی های مجاز نیست")
                    $(this).val("");
                }
            }
            return false;

        }
    });
    const inputs = Array.prototype.slice.call(
        document.querySelectorAll('.input_packing_code')
    );

    function focusNext() {
        const currInput = document.activeElement;
        const currInputIndex = inputs.indexOf(currInput);
        const nextinputIndex =
            (currInputIndex + 1) % inputs.length;
        const input = inputs[nextinputIndex];
        input.focus();
    }
</script>
