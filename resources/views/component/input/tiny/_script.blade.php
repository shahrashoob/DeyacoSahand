
<script src="{{asset("assets/plugins/tinymce/tinymce.min.js")}}"></script>
<script>
    tinymce.init({
        selector: 'textarea',
        plugins: [

        ],
        directionality:"rtl",
        toolbar: ' styleselect | bold italic | forecolor backcolor casechange permanentpen formatpainter removeformat | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link ',
        relative_urls: false,

    });


    // input
    let inputId = '';

    // set file link
    function fmSetLink($url) {
        document.getElementById(inputId).value = $url;
    }
    setTimeout( function () {
        $(".tox-button, .mce-close").click()
    },2000);
    setTimeout( function () {
        $(".tox-button, .mce-close").click()
    },4000);
    setTimeout( function () {
        $(".tox-button, .mce-close").click()
    },8000);
</script>
