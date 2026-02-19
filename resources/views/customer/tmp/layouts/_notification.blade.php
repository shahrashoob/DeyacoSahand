

<script>
    function notify( type,message,title="",icon="",url='',from='bottom', align="right",  animIn="animated bounceInLeft", animOut="animated rotateOut") {
        
        var title1="";
        if(title==""){
            switch (type) {
                case "danger":
                    title="خطایی رخ داده است";
                    break;
                case "success":
                    title="عمليات موفقيت آمیز بود";
                    break;
                case "warning":
                    title="هشدار";
                    break;
                case "info":
                    title="اطلاح";
                    break;
            }
        }
        var icon1="";
        if(icon==""){
            switch (type) {
                case "danger":
                    icon="fa fa-bug";
                    break;
                case "success":
                    icon="fa fa-check";
                    break;
                case "warning":
                    icon="fa fa-exclamation-triangle";
                    break;
                case "info":
                    icon="fa fa-info";
                    break;
            }
        }
        $.growl({
            icon: icon+icon1,
            title: title+title1,
            message:message,
            url: url
        }, {
            element: 'body',
            type: type,
            allow_dismiss: true,
            placement: {
                from: from,
                align: align
            },
            offset: {
                x: 30,
                y: 30
            },
            spacing: 10,
            z_index: 999999,
            delay: 5500,
            timer: 100,
            url_target: '_blank',
            mouse_over: false,
            animate: {
                enter: animIn,
                exit: animOut
            },
            icon_type: 'class',
            template: '<div data-growl="container" class="alert" role="alert" style="min-width:500px!important;">' +
                '<span data-growl="icon" class="fa-2x"></span> &nbsp;' +
                '<span data-growl="title" style="font-size: 18px; font-weight: bold"></span> <br/>' +
                '<span data-growl="message" style="font-size: 16px;"></span>' +
                '<a href="#!" data-growl="url"></a>' +
                '</div>'
        });
    };
</script>
<script>
@if ($errors->any())

            @foreach ($errors->all() as $error)
                <div class="alert alert-danger ">{{$error}}</div>
                    // notify( "danger",'{{$error}}');
            @endforeach

@endif

            @if($message = \Session::get('success'))
                        notify( "success",'{{$message}}');
            @endif
            @if($message = \Session::get('warning'))
                        notify( "warning",'{{$message}}');
            @endif
            @if($message = \Session::get('info'))
                        notify( "info",'{{$message}}');
            @endif
            @if($message = \Session::get('primary'))
                        notify( "primary",'{{$message}}');
            @endif
</script>


{{--<script type="text/javascript">--}}

{{--    $('.notifications.btn').on('click', function(e) {--}}
{{--        e.preventDefault();--}}
{{--        var nFrom = $(this).attr('data-from');--}}
{{--        var nAlign = $(this).attr('data-align');--}}
{{--        var nIcons = $(this).attr('data-notify-icon');--}}
{{--        var nType = $(this).attr('data-type');--}}
{{--        var nAnimIn = $(this).attr('data-animation-in');--}}
{{--        var nAnimOut = $(this).attr('data-animation-out');--}}
{{--        notify(nType,"عمشب سب سیب یسب یسب ");--}}
{{--    });--}}
{{--</script>--}}
