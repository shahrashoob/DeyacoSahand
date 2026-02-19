@extends('hr.employment.register.layout._layout',["title_caption"=>"تکمیل اطلاعات اولیه"])

@section('content')
    @include('component.input.datepicker.jalali_datepicker._style')
    <div class="col-md-12 content-class">
        @if($employment->status_personal_id!=4641402)
        <form id="form1" method="post"
              action="{{route('hr.employment.register.personal_info.submit',$employment->key)}}"
              enctype="multipart/form-data" autocomplete="false">
            @csrf

            @php $panel_name="personal_info" @endphp

                @include("hr.employment.register.personal_type.".$employment->personal_type_id.".cooperation_type.".$employment->cooperation_type_id."._personal_info")


            <div class="center">
                <br/>

                <a class="btn  mb-4" href="{{route("hr.employment.register.start.index",$employment->key)}}">بازگشت</a>
                <button class="btn btn-primary shadow-2 mb-4">ثبت و ادامه</button>
                <br/>
            </div>

        </form>
        @else
            @php $panel_name="personal_info" @endphp
            @include("hr.employment.register.personal_type.".$employment->personal_type_id.".cooperation_type.".$employment->cooperation_type_id."._show")
            <div class="center">
                <br/>
                <a class="btn  mb-4" href="{{route("hr.employment.register.start.index",$employment->key)}}">بازگشت</a>
                <a class="btn btn-primary shadow-2 mb-4" href="{{route("hr.employment.register.address.index",$employment->key)}}">بعدی</a>
                <br/>
            </div>
        @endif
    </div>
    @include('component.input.datepicker.jalali_datepicker._script')
@endsection
@section("scripts")

    <script type="text/javascript">
        $('#form1').validate({
            rules: {
                email: {
                    required: true,
                    minlength: 8, maxlength: 20,
                    letters_is_en:true,
                },
              //  post_id: {required: true},
                firstname: {required: true,letters_is_fa:true,},
                company_name: {required: true},
                lastname: {required: true,letters_is_fa:true,},
                father_name: {required: true,letters_is_fa:true,},
                birth_certificate_number: {required: true, number: true},
                gender_id: {required: true},
                register_code:{required: true},
                marital_status_id: {required: true},
                place_of_birth: {required: true},
                date_of_birth_value: {required: true},
                date_of_readiness_to_start_work_value: {required: true},
                @if(!isset($employment->worker->image->id))
                user_image_file_id: {required: true},
                @endif


            }
        });

    </script>

@endsection
