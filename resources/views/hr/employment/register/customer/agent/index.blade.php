@extends('hr.employment.register.layout._layout',["title_caption"=>"نمایندگان"])

@section('content')

{{--    <div class="col-md-12 content-class">--}}
{{--        <form id="form1" method="post"--}}
{{--              action="{{route('hr.employment.register.customer.agent.submit',$employment->key)}}"--}}
{{--              enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">--}}
{{--            @csrf--}}

            @php $panel_name="agent_customer" @endphp


            @include("hr.employment.register.customer.agent._list")

            <div class="center">
                <br/>
                <a class="btn  mb-4" href="{{route("hr.employment.register.address.index",$employment->key)}}">بازگشت</a>
                <a class="btn btn-primary shadow-2 mb-4" style="color: white" href="{{route("hr.employment.register.other.index",$employment->key)}}">بعدی</a>
                <br/>
            </div>
{{--        </form>--}}
{{--    </div>--}}

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

