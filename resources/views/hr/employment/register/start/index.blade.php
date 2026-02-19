@extends('hr.employment.register.layout._layout',["title_caption"=>"ثبت درخواست همکاری"])

@section('content')

    <div class="col-md-6 content-class">
        <form id="form1" method="post" action="{{route('hr.employment.register.start.submit',$employment->key??"")}}"
              autocomplete="false">
            @csrf
            @include("component.input._select", [
                        "id"=>"country_id",
                        "label"=>"تابعیت",
                        "option"=>$country_option["items"],
                        "val"=>$country_option["value"],
                        "text"=>$country_option["text"],
                        "class_col"=>"",
                    ])
            <div class="w-100"><br/></div>
            @include("component.input._select", [
                        "id"=>"cooperation_type_id",
                        "label"=>"نوع همکاری با سازمان",
                        "option"=>$cooperation_type_option["items"],
                        "val"=>$cooperation_type_option["value"],
                        "text"=>$cooperation_type_option["text"],
                        "class_col"=>"",
                    ])
            <div class="w-100"><br/></div>
            @include("component.input._select", [
                       "id"=>"personal_type_id",
                       "label"=>"نوع شخصیت",
                       "option"=>$personal_type_option["items"],
                       "val"=>$personal_type_option["value"],
                       "text"=>$personal_type_option["text"],
                       "class_col"=>"",
                   ])
            <div class="w-100"><br/></div>
            <div id="div_national_code">
                @include("component.input._hidden", ["id"=>"national_code_type", "value"=>0])
                @include("component.input._text", ["id"=>"national_code", 'label'=>"کد ملی/شناسه ملی/کد فراگیر  ", "value"=>$employment->national_code??"", "class_col"=>""])
            </div>


            <div id="div_mobile">
            @include("component.input._mobile", ["id"=>"mobile", 'label'=>"تلفن همراه (بدون صفر) ", "value"=>$employment->mobile??"","country_option"=>$mobile_country_option, "class_col"=>""])
            </div>

            <div style="text-align: left;margin-top: -10px;margin-bottom: 15px ">
                <a style=" color: #0a6aa1!important;font-size:12px;font-weight: unset"
                   href="{{route("login")}}">بازگشت</a>
            </div>
            @if($employment)
                <button id="btn_submit" class="btn btn-primary shadow-2 mb-4">تکمیل اطلاعات درخواست</button>
            @else
                <button id="btn_submit" class="btn btn-primary shadow-2 mb-4">ثبت و ادامه</button>
            @endif
            <br/>

        </form>
    </div>

@endsection
@section("scripts")

    <script type="text/javascript">

        $('#form1').validate({
            rules: {
                national_code: {required: true, national_code_personal: true, national_code_company:true,minlength: 5},
                personal_type_id: "required",
                cooperation_type_id: "required",
                nationality_id: "required",
                melicode: {required: true},
                mobile: {required: true,minlength: 10,maxlength: 10},
            }
        });
        $("#personal_type_id,#country_id,#cooperation_type_id").change(function () {

            national_code_validation();
        });

        $("#btn_submit").click(function () {
            return true;
        })

        function national_code_validation() {
            personal_type_id = parseInt($("#personal_type_id").val());
            cooperation_type_id = parseInt($("#cooperation_type_id").val());
            national_code = parseInt($("#national_code").val());
            country_id = $("#country_id").val();
            origin_country_id = {{$origin_country_id}};

            national_id = country_id == origin_country_id ? 1 : 2;
            caption = "کد ملی / شناسه ملی"


            // مواردی که فقط می تواند حقیقی باشد.
            cooperation_type_where_only_personal = [1, 11, 21, 31, 61, 4];
            if (cooperation_type_where_only_personal.includes(cooperation_type_id)) {
                if ($("#personal_type_id").val() == 2) {
                    $("#personal_type_id").val('')
                }
                $("#personal_type_id option:nth-child(odd)").css("display", "none")
            } else {

                $("#personal_type_id option:nth-child(odd)").css("display", "")
            }
            valied_code = true;
            caption_mobile="تلفن همراه (بدون صفر)";
            switch (personal_type_id) {
                case 1: // حقیقی
                    caption_mobile="تلفن همراه (بدون صفر)";
                    switch (national_id) {
                        case 1: // مبدا
                            caption = "کد ملی"
                            valied_code = checkCodeMeli(national_code)
                            $("#national_code_type").val(1)
                            break;
                        case 2: // اتباع
                            caption = "کد فراگیر"
                            $("#national_code_type").val(-1)
                            break;
                    }
                    break;
                case 2: // حقوقی
                    caption_mobile="تلفن همراه مدیر عامل (بدون صفر)";
                    switch (national_id) {
                        case 1: // مبدا
                            caption = "شناسه ملی"

                            $("#national_code_type").val(2)
                            break;
                        case 2: // اتباع
                            caption = "شناسه شرکت"
                            $("#national_code_type").val(-1)
                            break;
                    }
                    break;
            }
            $("#mobile_label").text(caption_mobile);
            $("#div_national_code label:nth-child(1)").text(caption);

            return valied_code;
        }

        national_code_validation();
    </script>

@endsection
