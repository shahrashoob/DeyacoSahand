@extends('hr.employment.register.layout._layout',["title_caption"=>"تایید قرارداد پیمانکار"])

@section('content')

    <div class="col-md-12 content-class">
        <form id="form1" method="post"
              action="{{route('hr.employment.register.contractor.confirm_drafting_contract.submit',$employment->key)}}"
              enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
            @csrf

            @php $panel_name="confirm_drafting_contract_contractor" @endphp
            @include("hr.employment.register.contractor.confirm_drafting_contract._info")


            <div class="center">
                <br/>
                <a class="btn  mb-4" href="{{route("hr.employment.register.start.index",$employment->key)}}">بازگشت</a>
                <button class="btn btn-primary shadow-2 mb-4"
                        onclick="return confirm('آیا از تایید قراداد اطمینان دارید؟')"
                >تایید قرارداد</button>

                <br/>
            </div>
        </form>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

