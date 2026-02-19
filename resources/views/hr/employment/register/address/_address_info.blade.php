@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")

    @include("component.input._select", [
      "id"=>"country_id",
      "label"=>"کشور",
      "option"=>$country_option["items"],
      "val"=>$country_option["value"],
      "text"=>$country_option["text"],
      "class_col"=>"",
      "mark"=>"*"
      ])<br/>
    @include("component.input._select", [
          "id"=>"province_id",
          "label"=>"استان",
          "option"=>$province_option["items"],
          "val"=>$province_option["value"],
          "text"=>$province_option["text"],
          "class_col"=>"",
          "mark"=>"*"
          ])<br/>

    @include("component.input._text", ["id"=>"city_name", 'label'=>"شهرستان","value"=> $request["city_name"]??"", "class_col"=>"","mark"=>"*"])
    @include("component.input._number", ["id"=>"phone", 'label'=>$employment->personal_type_id == 1?"شماره ثابت":"شماره ثابت شرکت", "value"=> $request["phone"]??"", "class_col"=>"","mark"=>"*"])
    @include("component.input._number", ["id"=>"postal_code", 'label'=>"کدپستی","value"=>$request["postal_code"]??"", "class_col"=>"","mark"=>"*"])

    @include("component.input._textarea", ["id"=>"address", 'label'=>"نشانی",  "value"=> $request["address"]??"", "class_col"=>"","mark"=>"*"])


@if(in_array($employment->cooperation_type_id,[1,11]))
    @if($post_document_receive_step_confirm)
        <p class="alert-warning">تحویل مدارک زیر به بایگانی الزامی می باشد.لطفا اصل مدارک را در زمان تحویل به همراه داشته باشید.</p>
    @endif
    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_receive_step_document_type_list])
@endif
@endsection
