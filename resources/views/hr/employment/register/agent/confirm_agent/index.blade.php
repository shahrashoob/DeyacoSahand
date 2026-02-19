@extends('hr.employment.register.layout._layout',["title_caption"=>"تایید نمایندگی"])

@section('content')

    <div class="col-md-6 content-class">
        <form id="form1" method="post"
              action="{{route('hr.employment.register.agent.confirm_agent.submit',[$agent, $agent->active_code])}}"
              autocomplete="false">
            @csrf
            <p style="text-align: justify" class="alert alert-warning">

                {{ $agent->worker->fullname("with_gender_2")}}
                با توجه به اینکه
                {{$company_name}}
                در
                {{$software_name}}
{{--                به عنوان--}}
{{--                {{$agent->employment->cooperation_type->caption}}--}}
                ثبت نام نموده است و شمارا به عنوان
                {{$agent->agent_type->caption}}
                انتخاب نموده است. لطفا در صورت تایید، باوارد کردن کد فعال سازی با این درخواست موافقت کنید.
            </p>
            @include("component.input._number", ["id"=>"verification_code", 'label'=>"لطفا کد فعال سازی را وارد نمایید. ", "value"=>"", "class_col"=>""])

            <div class="center">
                <br/>
                <a class="btn btn-primary shadow-2 mb-4"
                   href="{{route("hr.employment.register.agent.confirm_agent.reply",[$agent, $agent->active_code])}}">ارسال
                    کد</a>
                <button class="btn btn-primary shadow-2 mb-4">ثبت</button>
                <br/>
            </div>
            <br/>

        </form>
    </div>

@endsection
@section("scripts")

    <script type="text/javascript">

        $('#form1').validate({
            rules: {
                verification_code: {required: true, minlength: 5, maxlength: 5},
            }
        });

    </script>

@endsection