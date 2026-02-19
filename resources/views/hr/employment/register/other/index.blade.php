@extends('hr.employment.register.layout._layout',["title_caption"=>"سایر توضیحات"])

@section('content')

    <div class="col-md-12 content-class">
        @if( $employment->status_id!=4640108)
        <form id="form1" method="post"
              action="{{route('hr.employment.register.other.submit',$employment->key)}}" enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
            @csrf

            @php $panel_name="other" @endphp
                @include("hr.employment.register.other._info")

                <div class="center">
                <button class="btn btn-primary shadow-2 mb-4">ثبت و ادامه</button>
                <br/>
                </div>

        </form>
        @else
            @php $panel_name="other" @endphp
            @include("hr.employment.register.other._preview")
            <div class="center">
                <br/>
                <a class="btn btn-primary shadow-2 mb-4" href="{{route("hr.employment.register.confirm_information.index",$employment->key)}}">تایید نهایی</a>
                <br/>
            </div>
        @endif
    </div>

@endsection

