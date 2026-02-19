@extends('hr.employment.register.layout._layout',["title_caption"=>"ورود به درخواست همکاری"])

@section('content')

    <div class="col-md-6 content-class">
        <form id="form1" method="post" action="{{route('hr.employment.register.start.submit_link',$employment->key??"")}}"
              autocomplete="false">
            @csrf
            @include("component.input._lable", ["id"=>"firstname", 'label'=>"نام ", "value"=>$employment->worker->firstname??"", "class_col"=>""])
            @include("component.input._lable", ["id"=>"lastname", 'label'=>"نام خانوادگی", "value"=>$employment->worker->lastname??"", "class_col"=>""])
            @include("component.input._lable", [ 'label'=>"کدملی", "value"=>$employment->national_code ??"", "class_col"=>""])
            @include("component.input._lable", [ 'label'=>"نوع همکاری", "value"=>$employment->cooperation_type->caption ??"", "class_col"=>""])


            <div class="center">
                <button id="btn_submit" class="btn btn-primary shadow-2 mb-4">ارسال کد تایید</button>
            </div>
            <br/>
        </form>
    </div>

@endsection


