@extends('hr.employment.register.layout._layout',["title_caption"=>"بارگذاری مدارک"])

@section('content')

    <div class="col-md-12 content-class" >
        <form id="form1" method="post"
              action="{{route('hr.employment.register.upload_final_document.submit',$employment->key)}}"enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
            @csrf

            @php $panel_name="upload_final_document" @endphp

            @include("hr.employment.register.personal.upload_final_document._info")


            <div class="center">
                <br/>
                <a class="btn  mb-4" href="{{route("hr.employment.register.start.index",$employment->key)}}">بازگشت</a>
                <button class="btn btn-primary shadow-2 mb-4">ثبت و ادامه</button>
                <br/>
            </div>


        </form>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

    <script type="text/javascript">
        $('#form1').validate({
            rules: {

            }
        });

    </script>

@endsection
