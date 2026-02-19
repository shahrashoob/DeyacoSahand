@extends('hr.employment.register.layout._layout',["title_caption"=>"تکمیل اطلاعات خدمت سربازی"])

@section('content')
    <div class="col-md-12 content-class">
        <form id="form1" method="post"
              action="{{route('hr.employment.register.personal.military_information.submit_upload',$employment->key)}}"
              enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
            @csrf
            @php $panel_name="military_information" @endphp
            @include("hr.employment.register.personal.military_information._document")

        </form>

    </div>


@endsection


@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
