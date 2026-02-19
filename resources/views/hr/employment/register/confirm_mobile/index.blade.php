@extends('hr.employment.register.layout._layout',["title_caption"=>"ثبت کد تایید"])

@section('content')

    <div class="col-md-6 content-class">
        <form id="form1" method="post" action="{{route('hr.employment.register.confirm_mobile.submit',$employment->key??"")}}"
              autocomplete="false">
            @csrf
            @include("hr.employment.register.personal_type._start_info")
            @include("component.input._number", ["id"=>"verification_code", 'label'=>"لطفا کد تایید را وارد نمایید ", "value"=>"", "class_col"=>""])


            <div style="text-align: left;margin-top: -10px;margin-bottom: 15px ">
                <a style=" color: #0a6aa1!important;font-size:12px;font-weight: unset"
                   href="{{route("hr.employment.register.start.index")}}">بازگشت</a>
            </div>
            <button id="btn_submit" class="btn btn-primary shadow-2 mb-4">ثبت و ادامه</button>

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
