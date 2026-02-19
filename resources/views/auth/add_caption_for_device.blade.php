<!DOCTYPE html>
<html lang="en">
<head>
    @include("layouts._head")

    <style>
        body {
            font-family: IRANSans !important;
        }

        .mb-4, .my-4 {

        }
    </style>
</head>
<body>
<div class="auth-wrapper">

    <div class="auth-content subscribe">
        @include("layouts._messages")
        <form id="form1" method="post" action="{{route('submit_add_caption_for_device',[$worker,$user_device])}}"
              autocomplete="false">
            @csrf
            <div class="card">
                <div class="row no-gutters">
                    <div class="col-md-12">
                        <h4 style="text-align: center; font-weight: bold;
    margin-top: 50px;
    margin-bottom: 10px; " class="mb-4"> نام گذاری دستگاه</h4>
                    </div>
                    <div class="col-md-12 col-lg-12">
                        <div class="card-body">
                            <div class="row justify-content-center">

                                <div class="col-sm-6">

                                    <div class="alert alert-primary">
                                       لطفا نام دستگاهی که با آن وارد شده اید را مشخص نمایید
                                    </div>
                                    @include("component.input._text",[
                                              "id"=>"caption",
                                              "label"=>"نام دستگاه",
                                               "mark"=>"*",
                                               "class_col"=>"col-md-12"
                                              ])

                                    <br/>

                                    {{--                                    <div style="text-align: left;margin-top: -20px;margin-bottom: 15px ">--}}
                                    {{--                                        <a style=" color: #0a6aa1!important;font-size:12px;font-weight: unset" href="{{route('login')}}" >ورود به سامانه</a>--}}
                                    {{--                                    </div>--}}
                                    <button class="btn btn-primary shadow-2 mb-4" style="width: 100%;padding: 5px">ثبت
                                    </button>
                                    <br/>
                                    <br/>

                                </div>

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
        </form>
    </div>
</div>

@include("layouts._footer")

</body>


</html>
@section("scripts")

    <script type="text/javascript">
        $('#form1').validate({
            rules: {
                caption: {required: true,},



            }
        });

    </script>

@endsection
