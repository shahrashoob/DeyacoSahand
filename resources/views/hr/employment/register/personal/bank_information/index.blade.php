@extends('hr.employment.register.layout._layout',["title_caption"=>"  ثبت اطلاعات بانکی"])

@section('content')

    <div class="col-md-12 content-class">
        <form id="form1" method="post"
              action="{{route('hr.employment.register.personal.bank_information.submit',$employment->key)}}"
              enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
            @csrf

            @php $panel_name="bank_information" @endphp
            @include("hr.employment.register.personal.bank_information._info")<br/>
            @include("hr.employment.register.personal.bank_information._list")

        </form>
        <div class="center">
        @if($employment->worker->absorption_type_id==1)

                <br/>
                <a class="btn  mb-4" href="{{route("hr.employment.register.personal.work_medicine.index",$employment->key)}}">بازگشت</a>
                @else
                <br/>
                <a class="btn  mb-4" href="{{route("hr.employment.register.start.index",$employment->key)}}">بازگشت</a>
                @endif
                <a class="btn btn-primary mb-4 text-white"
                   href="{{route("hr.employment.register.personal.confirm_drafting_contract.index",$employment->key)}}">تایید و مرحله بعد</a>

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
                bank_id: "required",
                bank_information_file_id:"required",
                shaba_number: {
                    required: true,
                    minlength: 26, maxlength: 26,
                },
                card_number: {
                    minlength: 16, maxlength: 16,
                },
            }
        });

    </script>

@endsection