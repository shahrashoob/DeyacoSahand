<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>دریافت کاتالوگ دیاکو</title>
    <!-- CSS Stylesheets -->
    <link rel="stylesheet"  href="{{url("catalog/css/bootstrap4-rtl.min.css")}}">
    <link rel="stylesheet"  href="{{url("catalog/css/font-awesome.min.css")}}">
    <link rel="stylesheet"  href="{{url("catalog/css/style.css")}}" >
    <link rel="stylesheet"  href="{{url('assets/fonts/fontiran/fontiran.css')}}">
</head>
<body class="rtl">

<div class="contact1">

    <div class="container-contact1">
        <div class="contact1-pic js-tilt" data-tilt>
            <img src="{{asset("catalog/pics/img-01.png")}}" alt="IMG">
        </div>
        <form id="form1" autocomplete="off"
              action="{{route("catalog_submit")}}"
              method="post"
              novalidate="novalidate"

              class="contact1-form validate-form"
        >
            @csrf

            <span class="contact1-form-title">دریافت کاتالوگ</span>
            @if($message = \Session::get('success'))
                <div class="alert alert-success ">{!!$message!!}</div>
            @endif
            <div class="wrap-input1 validate-input" data-validate = "لطفا نام خود را وارد کنید!">
                <input class="input1" type="text" name="fullname" required placeholder="نام شما">
                <span class="shadow-input1"></span>
            </div>
            <div class="wrap-input1 validate-input"  data-validate = "لطفا شرکت خود را وارد کنید!">
                <input class="input1" type="text" required name="company" placeholder="شرکت یا سازمان">
                <span class="shadow-input1"></span>
            </div>
            <div class="wrap-input1 validate-input" data-validate = "لطفا موبایل خود را وارد کنید!">
                <input class="input1" type="text" name="mobile" min="10" required placeholder="شماره همراه">
                <span class="shadow-input1"></span>
            </div>

            <div class="container-contact1-form-btn">
                <button  type="submit" class="contact1-form-btn"  style="background:#e83e8c">
							<span id="submit">دریافت کاتالوگ
								<i class="fa fa-long-arrow-left" aria-hidden="true"></i>
							</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="{{asset("catalog/js/jquery-3.1.1.min.js")}}"></script>
<script  src="{{asset("catalog/js/scripts.js")}}"></script>
<script>
    $("#submit").click(function (){
        if($("#mobile").val()==""){
            alert("لطفا شماره موبایل را وارد نمایید.");
        }
        if($("#fullname").val()==""){
            alert("لطفا نام و نام خانوادگی  را وارد نمایید.");
        }
        if($("#company").val()==""){
            alert("لطفا نام شرکت  را وارد نمایید.");
        }
    })
</script>
</body><!-- This template has been downloaded from Webrubik.com -->
</html>
