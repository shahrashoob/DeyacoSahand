@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")

    @include("component.input._select", [
        "id"=>"bank_id",
        "label"=>"نام بانک",
        "option"=>$bank_names_option["items"],
        "class_col"=>"",
        'mark'=>"*"

    ])
    <br/>
    @include("component.input._text", ["id"=>"bank_branch", 'label'=>"شعبه بانک",  "class_col"=>"",'mark'=>"*"])
    @include("component.input._number", ["id"=>"card_number", 'label'=>"شماره کارت", "class_col"=>""])
    @include("component.input._number", ["id"=>"account_number", 'label'=>"شماره حساب",  "class_col"=>"",'mark'=>"*"])
    @include("component.input._text", ["id"=>"shaba_number", 'label'=>"شماره شبا", "class_col"=>"",'mark'=>"(شماره شبا باید شامل IR باشد. ) *"])
    <button class="btn btn-primary shadow-2 mb-4">ثبت </button>
@endsection

