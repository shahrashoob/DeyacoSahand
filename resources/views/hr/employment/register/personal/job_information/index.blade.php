@extends('hr.employment.register.layout._layout',["title_caption"=>"تکمیل اطلاعات سابقه شغلی"])

@section('content')
    @include('component.input.datepicker.jalali_datepicker._style')
    <div class="col-md-12 content-class">


        @if($employment->status_id==4640107)
            @php $panel_name="job_information" @endphp
            @include("hr.employment.register.personal.job_information._show")




        @elseif( $employment->status_job_information_id!=4641402 )
        <form id="form1" method="post"
              action="{{route('hr.employment.register.personal.job_information.submit',$employment->key)}}" enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
            @csrf

            @php $panel_name="job_information" @endphp


                @include("hr.employment.register.personal.job_information._info")

                @include("hr.employment.register.personal.job_information._list",["panel_type"=>"register"])


            <div style="float: left;padding-top: 20px">
                <a class="btn  mb-4" href="{{route('hr.employment.register.personal.academic_degree.index',$employment->key)}}">بازگشت</a>
                <a class="btn btn-primary mb-4 text-white" href="{{route("hr.employment.register.personal.educational_course.index",$employment->key)}}">
                    @if($employment->worker->user_job_informations->count()>0)
                    تایید و مرحله بعد
                    @else
                     مرحله بعد
                    @endif
                </a>

                <br/>
            </div>
        </form>
        @else
            @php $panel_name="job_information" @endphp
            @include("hr.employment.register.personal.job_information._show")
            <div class="center">
                <br/>
                <a class="btn  mb-4" href="{{route("hr.employment.register.personal_info.index",$employment->key)}}">بازگشت</a>
                <a class="btn btn-primary shadow-2 mb-4" href="{{route("hr.employment.register.personal.educational_course.index",$employment->key)}}">بعدی</a>
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
                start_date_of_work_value: 'required',
                end_date_of_work_value: 'required',
                post_caption: 'required',
                company_name_of_work: 'required',
                address_of_work: 'required',
                identifier_name: 'required',
                identification_number: {
                    required: true,
                    number: true,
                    minlength: 11, maxlength: 11
                },
            }
        });

    </script>

@endsection
