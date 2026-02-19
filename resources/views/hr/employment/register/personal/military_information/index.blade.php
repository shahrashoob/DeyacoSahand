@extends('hr.employment.register.layout._layout',["title_caption"=>"تکمیل اطلاعات سربازی"])

@section('content')

    <div class="col-md-12 content-class">
        @if( $employment->status_personal_id!=4641402)
            <form id="form1" method="post"
                  action="{{route('hr.employment.register.personal.military_information.submit',$employment->key)}}"
                  enctype="multipart/form-data" autocomplete="false">
                @csrf



                @php $panel_name="military_information" @endphp


                @include("hr.employment.register.personal.military_information._info")


                <div class="center">
                    <br/>
                    @if($employment->status_id==4640107)
                        <a class="btn  mb-4"
                           href="{{route("hr.employment.register.confirm_upload_document.index",$employment->key)}}">بازگشت</a>
                    @else

                        <a class="btn  mb-4"
                           href="{{route("hr.employment.register.personal.dependent.index",$employment->key)}}">بازگشت</a>
                    @endif
                    <button class="btn btn-primary shadow-2 mb-4">ثبت و ادامه</button>
                    <br/>
                </div>


            </form>
        @else
            @php $panel_name="military_information" @endphp
            @include("hr.employment.register.personal.military_information._show")
            <div class="center">
                <br/>
                @if($employment->status_id==4640107)
                    <a class="btn  mb-4"
                       href="{{route("hr.employment.register.personal.confirm_upload_document.index",$employment->key)}}">بازگشت</a>
                @else

                    <a class="btn  mb-4"
                       href="{{route("hr.employment.register.personal.dependent.index",$employment->key)}}">بازگشت</a>
                @endif
                <a class="btn btn-primary shadow-2 mb-4"
                   href="{{route("hr.employment.register.other.index",$employment->key)}}">بعدی</a>
                <br/>
            </div>

        @endif
    </div>

@endsection

@section("scripts")

    <script type="text/javascript">
        $('#form1').validate({
            rules: {
                military_information_id: "required",
            }
        });

    </script>

@endsection
