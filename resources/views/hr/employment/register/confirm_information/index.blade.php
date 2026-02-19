@extends('hr.employment.register.layout._layout',["title_caption"=>"تایید نهایی اطلاعات"])

@section('content')

    <div class="col-md-12 content-class">
        <form id="form1" method="post"
              action="{{route('hr.employment.register.confirm_information.submit',$employment->key)}}" enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
            @csrf

            @php $panel_name="confirm_information" @endphp
            @include("hr.employment.register.confirm_information._info")

            <div class="center">
                <a class="btn  mb-4" href="{{route('hr.employment.register.other.index',$employment->key)}}">بازگشت</a>
                <button class="btn btn-primary shadow-2 mb-4">تایید نهایی</button>
                <br/>
            </div>


        </form>
    </div>

@endsection

