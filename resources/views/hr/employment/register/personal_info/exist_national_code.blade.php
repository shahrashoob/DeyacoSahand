@extends('hr.employment.register.layout._layout',["title_caption"=>"ثبت درخواست همکاری"])

@section('content')
    @include('component.input.datepicker.jalali_datepicker._style')
    <div class="row">
        <div class="col-md-5">
            <img style="width: 200px;"
                 src="{{asset("chatify_app/users-avatar/".($employment->worker->image->filename??''))}}"
                 onerror="this.onerror=null;this.src='{{url("assets/images/avatar.png")}}';"
            />
        </div>
        <div class="col-md-7" style="text-align:right;"><br/>
            @include("component.input._lable", [
                           "label"=>"ملیت",
                           "value"=>$employment->nationality->caption,
                           "class_col"=>"col-md-12"
                       ])

            @include("component.input._lable", [
                      "label"=>"نوع همکاری با سازمان",
                      "value"=>$employment->cooperation_type->caption,
                      "class_col"=>"col-md-12"
                  ])

            @include("component.input._lable", [
                      "label"=>"نوع شخصیت",
                      "value"=>$employment->personal_type->caption,
                      "class_col"=>"col-md-12"
                  ])
            @include("component.input._lable", [
                      "label"=>"کد ملی / شناسه ملی",
                      "value"=>$employment->national_code,
                      "class_col"=>"col-md-12"
                  ])
        </div>
    </div>

    <div class="w-100"><br/></div>
    <div class="col-md-6 content-class">
        <form id="form1" method="post"
              action="{{route('hr.employment.register.personal_info.submit_exist_national_code',$employment->key)}}"
              enctype="multipart/form-data" autocomplete="false">
            @csrf
            @include("component.input._hidden", ["id"=>"nationality_id","value"=>$employment->nationality_id ])
            @include("component.input._hidden", ["id"=>"cooperation_type_id","value"=>$employment->cooperation_type_id ])
            @include("component.input._hidden", ["id"=>"personal_type_id","value"=>$employment->personal_type_id ])
            @include("component.input._hidden", ["id"=>"national_code","value"=>$employment->national_code ])

            @if(!$worker)
                @include("component.input._text", ["id"=>"email",'label'=>"نام کاربری ","value"=>"","class_col"=>"","mark"=>"(باید شامل حروف انگلیسی باشد ) *"])
            @endif

            @if(in_array($employment->cooperation_type_id,[1,11]))
                @include("component.input._file_upload", ["id"=>"user_image_file_id", 'label'=>$employment->personal_type->id == 1?"تصویر پرسنلی":"تصویر/لوگو","value"=> "", "class_col"=>"","mark"=>"*"])
            @endif

            @if(in_array($employment->cooperation_type_id,[6,61]) && $get_the_supplier_image)
                @include("component.input._file_upload", ["id"=>"user_image_file_id", 'label'=>$employment->personal_type->id == 1?"تصویر پرسنلی":"تصویر/لوگو","value"=> "", "class_col"=>"","mark"=>"*"])
            @endif

            @if(in_array($employment->cooperation_type_id,[3,31]) && $get_the_customer_image)
                @include("component.input._file_upload", ["id"=>"user_image_file_id", 'label'=>$employment->personal_type->id == 1?"تصویر پرسنلی":"تصویر/لوگو","value"=> "", "class_col"=>"","mark"=>"*"])
            @endif

            @if(in_array($employment->cooperation_type_id,[2,21]) && $get_the_contractor_image)
                @include("component.input._file_upload", ["id"=>"user_image_file_id", 'label'=>$employment->personal_type->id == 1?"تصویر پرسنلی":"تصویر/لوگو","value"=> "", "class_col"=>"","mark"=>"*"])
            @endif

            @if(in_array($employment->cooperation_type_id,[1,11])&&$employment->personal_type->id == 1)
                {{-- اگر نوع همکاری کارمند تمام وقت است، پست گرفته شود.--}}

                <div style="text-align: right" class="mb-3">
                    @include("component.input._select", [
                        "id"=>"post_id",
                        "label"=>"پست سازمانی",
                        "option"=>$empty_post_option["items"],
                        "class_col"=>"",
                        "mark"=>"*"

                    ])<br/>
                    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
                            "id"=>"date_of_readiness_to_start_work",
                            'label'=>"تاریخ آمادگی جهت شروع به کار",
                             'min_date'=>'today',
                             "value"=>$employment->date_of_readiness_to_start_work??null,
                             "class_col"=>"",
                             "mark"=>"*"
    ])
                </div>

            @endif


            <button class="btn btn-primary shadow-2 mb-4">ثبت و ادامه</button>
            <a class="btn  mb-4" href="{{route("login")}}">بازگشت</a>
            <br/>

        </form>
    </div>
    @include('component.input.datepicker.jalali_datepicker._script')
@endsection
@section("scripts")

    <script type="text/javascript">
        $('#form1').validate({
            rules: {
                national_code: "required",
                personal_type_id: "required",
                cooperation_type_id: "required",
                nationality_id: "required",
                date_of_readiness_to_start_work_value: "required",
                melicode: {required: true},
                post_id: "required",
                email: {
                    required: true,
                    minlength: 8, maxlength: 20
                },
            }
        });

    </script>

@endsection
