@extends('hr.employment.register.layout._layout',["title_caption"=>"  طب کار"])

@section('content')

    <div class="col-md-12 content-class">
        <form id="form1" method="post"
              action="{{route('hr.employment.register.personal.work_medicine.submit',$employment->key)}}"
              enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
            @csrf

            @php $panel_name="work_medicine" @endphp
            @include("hr.employment.register.personal.work_medicine._info")

        </form>
            <div class="center">
                <br/>
                <a class="btn  mb-4" href="{{route("hr.employment.register.start.index",$employment->key)}}">بازگشت</a>
                <a class="btn btn-primary mb-4 text-white"
                   href="{{route("hr.employment.register.personal.bank_information.index",$employment->key)}}">تایید و مرحله بعد</a>

            </div>

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
                bank_branch: "required",
                account_number: "required",
                shaba_number: "required",
                bank_name: "required",
                work_medicine_file_id:"required",
            }
        });

    </script>

@endsection