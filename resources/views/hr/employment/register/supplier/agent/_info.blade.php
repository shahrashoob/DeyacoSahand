@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")


    @include("component.input._number", [ 'label'=>'کدملی',"id"=>"national_code" ,"value"=>"", "class_col"=>""])
    @include("component.input._number", [ 'label'=>'تلفن همراه',"id"=>"mobile" ,"value"=>"", "class_col"=>""])

        @include("component.input._select",[
            "id"=>"agent_type_id",
            "label"=>"نوع ارتباط",
            "option"=>$agent_type_option["items"],
            "val"=>$agent_type_option["value"],
            "text"=>$agent_type_option["text"],
            "class_col"=>""
            ])

        @include("component.input._checkbox",["id"=>"has_the_right_to_sign",'label'=>"آیا حق امضا دارد؟","checked"=>$agent->has_the_right_to_sign??0])


        <button class="btn btn-primary shadow-2 mb-4">ثبت</button>
@endsection
