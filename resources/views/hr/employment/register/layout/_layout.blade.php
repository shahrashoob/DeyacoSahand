<!DOCTYPE html>
<html lang="en">
<head>
    @include("layouts._head")
    <style>
        body {
            font-family: IRANSans !important;
        }

        .content-class {
            text-align: right !important;
        }

        .subscribe {
            width: 950px !important;
        }
    </style>
    @include('component.input.datepicker._script')
    @yield("styles")
</head>
<body>
<div class="auth-wrapper">

    <div class="auth-content subscribe" style="overflow: auto">
        @include("layouts._messages")
        <div class="card">
            <div class="row no-gutters">
                <div class="col-md-12">
                    <h4
                        style="text-align: center; font-weight: bold;     margin-top: 50px;    margin-bottom: 10px; "
                        class="mb-4">
                        {{$title_caption}}
                    </h4>
                </div>
                <div class="col-md-12 col-lg-12">
                    <div class="card-body text-center">
                        <div class="row justify-content-center">
                            @yield("content")

                        </div>
                    </div>
                </div>
                {{--                    <div--}}
                {{--                        class="col-md-4 col-lg-6 d-none d-md-flex d-lg-flex  align-items-center justify-content-center">--}}
                {{--                        <img src="{{asset("assets/images/login_logo.png")}}" alt="lock images" class="img-fluid">--}}
                {{--                    </div>--}}
                <div class="col-md-12"
                     style="text-align: center;font-weight: bold; font-size: 12px; margin-top: -30px">طراحی و تولید
                    شرکت صنعتی دیاکو مهن آریا


                </div>

            </div>
        </div>
    </div>
</div>

@include("layouts._footer")
@yield("scripts")


</html>
