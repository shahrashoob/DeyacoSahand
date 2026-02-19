@extends('hr.employment.register.layout._layout',["title_caption"=>"تکمیل اطلاعات افراد تحت تکفل"])

@section('content')
    @include('component.input.datepicker.jalali_datepicker._style')
    <div class="col-md-12 content-class">
        @if($employment->status_id==4640107)
            @php $panel_name="dependent" @endphp
            @include("hr.employment.register.personal.dependent._show")

        @elseif( $employment->status_dependent_id!=4641402)
            <form id="form1" method="post"
                  action="{{route('hr.employment.register.personal.dependent.submit',$employment->key)}}"
                  enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
                @csrf

                @php $panel_name="dependent" @endphp

                @include("hr.employment.register.personal.dependent._info")

                @include("hr.employment.register.personal.dependent._list",["panel_type"=>"register"])

                <div style="float: left;padding-top: 20px">
                    <a class="btn  mb-4" href="{{route("hr.employment.register.personal.educational_course.index",$employment->key)}}">بازگشت</a>

                    @if(!empty($employment->worker->gender_id))
                        @if($employment->worker->gender_id==1 && $employment->nationality_id==1 )
                            <a class="btn btn-primary mb-4 text-white"
                               href="{{route("hr.employment.register.personal.military_information.index",$employment->key)}}">مرحله بعد</a>
                            <br/>
                        @else
                            <a class="btn btn-primary mb-4 text-white"
                               href="{{route("hr.employment.register.other.index",$employment->key)}}">مرحله بعد</a>
                            <br/>
                        @endif
                    @endif


                    <br/>
                </div>
            </form>
        @else
            @php $panel_name="dependent" @endphp
            @include("hr.employment.register.personal.dependent._show")
            <div class="center">
                <br/>
                <a class="btn  mb-4"
                   href="{{route("hr.employment.register.personal.educational_course.index",$employment->key)}}">بازگشت</a>
                @if(!empty($employment->worker->gender_id))
                    @if($employment->worker->gender_id==1 && $employment->nationality_id==1 )
                        <a class="btn btn-primary shadow-2 mb-4"
                           href="{{route("hr.employment.register.personal.military_information.index",$employment->key)}}">بعدی</a>
                        <br/>
                    @else
                        <a class="btn btn-primary shadow-2 mb-4"
                           href="{{route("hr.employment.register.other.index",$employment->key)}}">بعدی</a>
                        <br/>
                    @endif
                @endif
            </div>

        @endif

    </div>
    @include('component.input.datepicker.jalali_datepicker._script')

@endsection


@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

    <script type="text/javascript">
        $('#form1').validate({
            rules: {
                dependent_type_id: 'required',
                first_name: 'required',
                last_name: 'required',
                date_of_birth_value: 'required',
                national_code: {required: true, number: true},
            }
        });

    </script>

@endsection
