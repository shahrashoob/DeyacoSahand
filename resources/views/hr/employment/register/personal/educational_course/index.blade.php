@extends('hr.employment.register.layout._layout',["title_caption"=>"تکمیل دوره های آموزشی"])

@section('content')
    @include('component.input.datepicker.jalali_datepicker._style')
    <div class="col-md-12 content-class">
        @if($employment->status_id==4640107)
            @php $panel_name="educational_course" @endphp
            @include("hr.employment.register.personal.educational_course._show")

        @elseif( $employment->status_educational_course_id!=4641402)
            <form id="form1" method="post"
                  action="{{route('hr.employment.register.personal.educational_course.submit',$employment->key)}}"
                  enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
                @csrf

                @php $panel_name="educational_course" @endphp

                @include("hr.employment.register.personal.educational_course._info")

                @include("hr.employment.register.personal.educational_course._list",["panel_type"=>"register"])

                <div style="float: left;padding-top: 20px">
                    <a class="btn  mb-4"
                       href="{{route('hr.employment.register.personal.job_information.index',$employment->key)}}">بازگشت</a>

                        <a class="btn btn-primary mb-4 text-white"
                           href="{{route("hr.employment.register.personal.dependent.index",$employment->key)}}">
                            @if($employment->worker->user_educational_courses->count()>0)

                                تایید و مرحله بعد
                            @else
                                مرحله بعد
                            @endif
                        </a>

                    <br/>
                </div>
            </form>
        @else
            @php $panel_name="educational_course" @endphp
            @include("hr.employment.register.personal.educational_course._show")
            <div class="center">
                <br/>
                <a class="btn  mb-4" href="{{route("hr.employment.register.personal_info.index",$employment->key)}}">بازگشت</a>
                    <a class="btn btn-primary shadow-2 mb-4"
                       href="{{route("hr.employment.register.personal.dependent.index",$employment->key)}}">بعدی</a>
                    <br/>

                @endif
            </div>

    </div>


    @include('component.input.datepicker.jalali_datepicker._script')

@endsection
@section("scripts")

    <script type="text/javascript">
        $('#form1').validate({
            rules: {
                course_name: 'required',
                name_of_institution: 'required',
                duration: 'required',
                start_date_value: 'required',
                end_date_value: 'required',
            }
        });

    </script>

@endsection
