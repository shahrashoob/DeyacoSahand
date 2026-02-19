


    <title>  {{$setting["software_name"]->string_value??""}}</title>


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="{{config("system.panel_description")}}" />
    <meta name="author" content="ArjNet.ir" />

    <link rel="icon" href="{{asset('assets/images/favicon.ico')}}" type="image/x-icon">

    <link rel="stylesheet" href="{{asset('assets/fonts/fontawesome-free-5.11.2-web/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fonts/fontawesome-free-5.11.2-web/css/all.min.css')}}">

    <link rel="stylesheet" href="{{asset('assets/plugins/animation/css/animate.min.css')}}">

    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">

    <link rel="stylesheet" class="rtl-css" href="{{asset('assets/css/layouts/'.__("rtl").'.css')}}">

    <link rel="stylesheet" class="rtl-css" href="{{asset('assets/fonts/fontiran/fontiran.css')}}">

    <!-- Notification css -->
    <link  rel="stylesheet" href="{{asset('assets/plugins/notification/css/notification.min.css')}}">

    <style>
        .error{
            color:red !important;
            display:block;
        }
        .tbl_product input {
            width: 100px;
        }

        .tbl_product {
            background: #f8f8f8;
        }
        .nav-item{
            color:#efefef;
        }
        .center{
            text-align: center!important;
            vertical-align: middle!important;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

    </style>

    <script src="{{asset("assets/plugins/jquery/js/jquery.min.js")}}" ></script>
