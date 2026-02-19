@extends('hr.employment.register.layout._layout',["title_caption"=>"تکمیل اطلاعات آدرس"])

@section('content')
    @include('component.input.datepicker.jalali_datepicker._style')
    <div class="col-md-12 content-class">
        <form id="form1" method="post"
              action="{{route('hr.employment.register.address.submit_upload',$employment->key)}}"
              enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
            @csrf
            @php $panel_name="address" @endphp
            @include("hr.employment.register.address._document")

        </form>

    </div>
    @include('component.input.datepicker.jalali_datepicker._script')

@endsection


@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
