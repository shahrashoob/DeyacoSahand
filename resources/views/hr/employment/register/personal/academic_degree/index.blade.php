@extends('hr.employment.register.layout._layout',["title_caption"=>"تکمیل اطلاعات تحصیلی"])

@section('content')
    @include('component.input.datepicker.jalali_datepicker._style')
    <div class="col-md-12 content-class">
        @if($employment->status_id==4640107)
            @php $panel_name="academic_degree" @endphp
            @include("hr.employment.register.personal.academic_degree._show")

        @elseif( $employment->status_academic_degree_id!=4641402)
            <form id="form1" method="post"
                  action="{{route('hr.employment.register.personal.academic_degree.submit',$employment->key)}}"
                  enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
                @csrf

                @php $panel_name="academic_degree" @endphp

                @include("hr.employment.register.personal.academic_degree._info")

                @include("hr.employment.register.personal.academic_degree._list",["panel_type"=>"register"])

                <div style="float: left;padding-top: 20px">
                    <a class="btn  mb-4" href="{{route("hr.employment.register.address.index",$employment->key)}}">بازگشت</a>
                    @if ($employment->worker->user_academic_degrees->count() > 0)
                        <a class="btn btn-primary mb-4 text-white"
                           href="{{route("hr.employment.register.personal.job_information.index",$employment->key)}}">تایید و
                            مرحله
                            بعد</a>
                    @endif

                    <br/>
                </div>
            </form>
        @else
            @php $panel_name="academic_degree" @endphp
            @include("hr.employment.register.personal.academic_degree._show")
            <div class="center">
                <br/>
                <a class="btn  mb-4" href="{{route("hr.employment.register.address.index",$employment->key)}}">بازگشت</a>
                <a class="btn btn-primary shadow-2 mb-4"
                   href="{{route("hr.employment.register.personal.job_information.index",$employment->key)}}">بعدی</a>
                <br/>
            </div>

        @endif

    </div>


@endsection


@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")
    @include('component.input.datepicker.jalali_datepicker._script')
    <script type="text/javascript">
        $('#form1').validate({
            rules: {
                academic_degree_type_id: 'required',
                feild_of_academic_degree_id_auto: 'required',
                name_of_academic_degree: 'required',
                start_date_value: 'required',
                end_date_value: 'required',
                average: {
                    required: true,
                    range: [0, 20]
                },

            }
        });

    </script>

@endsection
