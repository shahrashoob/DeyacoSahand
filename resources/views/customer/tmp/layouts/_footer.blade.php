<!-- footer -->

<script src="{{asset('assets/js/vendor-all.min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/js/pcoded.min.js')}}"></script>

<script src="{{asset('assets/plugins/jquery-validation/js/jquery.validate.min.js?random=7')}}"></script>
<script src="{{asset('assets/plugins/jquery-validation-1.11.1/localization/messages_'.__("local").'.js')}}"></script>
<!-- Notification Js -->
<script src="{{asset('assets/plugins/notification/js/bootstrap-growl.min.js')}}"></script>
<!-- persiannumber js -->
<script src="{{asset('assets/js/persianumber.min.js')}}"></script>

<!-- /footer -->
<script>
    $("#menu-fixed").click(function () {

    });
    @if(!isset($keypress_enable))
    $('form').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;

        }
    });
    @endif

    @if(!isset($no_persian) && __("local")=="fa")
    $(document).ready(function () {
        $('*').persiaNumber();
    });
    @endif


</script>

@include("component._spinner",["id"=>".btn_action"])

