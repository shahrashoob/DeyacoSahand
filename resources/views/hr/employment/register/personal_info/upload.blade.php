@extends('hr.employment.register.layout._layout',["title_caption"=>"تکمیل اطلاعات شخصی"])

@section('content')
    @include('component.input.datepicker.jalali_datepicker._style')
    <div class="col-md-12 content-class">
        <form id="form1" method="post"
              action="{{route('hr.employment.register.personal_info.submit_upload',$employment->key)}}"
              enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
            @csrf
            @php $panel_name="personal_info" @endphp
            @include("hr.employment.register.personal_type.".$employment->personal_type_id.".cooperation_type.".$employment->cooperation_type_id."._document")

        </form>

    </div>
    @include('component.input.datepicker.jalali_datepicker._script')

@endsection


@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
