@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")

    @include("hr.employment.register.personal_type._start_info")


    @include("component.input._select", [
        "id"=>"post_id",
        "label"=>"پست سازمانی",
        "option"=>$empty_post_option["items"],
         "val"=>$empty_post_option["value"],
         "text"=>$empty_post_option["text"],
        "class_col"=>"",
        "mark"=>"*"

    ])

    <div class="w-100"><br/></div>


    @include("hr.employment.register.personal_type._basic_personal_info")

    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
        "id"=>"date_of_readiness_to_start_work",
        'label'=>"تاریخ آمادگی جهت شروع به کار",
         'min_date'=>'today',
         "value"=>$employment->date_of_readiness_to_start_work??null,
         "class_col"=>"",
         "mark"=>"*"
        ])
    @include("component.input._file_upload", ["id"=>"user_image_file_id", 'label'=>"تصویر پرسنلی","value"=> "", "class_col"=>"","mark"=>"*"])
    @if(in_array($employment->cooperation_type_id,[1,11]))
    @if($post_document_receive_step_confirm)
        <p class="alert-warning">تحویل مدارک زیر به بایگانی الزامی می باشد.لطفا اصل مدارک را در زمان تحویل به همراه داشته باشید.</p>
    @endif
    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_receive_step_document_type_list])
    @endif
@endsection
