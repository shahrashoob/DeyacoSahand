@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")

    @include("component.input._lable", [ "label"=>"نوع ارتباط با فرد", "value"=>$user_dependent->dependent_type->caption,  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"نام و نام خانوادگی",  "value"=>$user_dependent->fullname(),  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>($employment->nationality_id==2 &&$employment->worker->user_address()->first()->address->country_id==112) ?"کد فراگیر" :"کدملی",
              "value"=>$user_dependent->national_code,
               "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"تاریخ تولد",  "value"=>$user_dependent->get_date_of_birth(),  "class_col"=>"col-md-12" ])

    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_receive_step_document_type_list])
    @if($employment->status_id==4640107)
        <a class="btn  mb-4"
           href="{{route("hr.employment.register.personal.confirm_upload_document.index",$employment->key)}}">بازگشت</a>
    @else

        <a class="btn  mb-4"
           href="{{route("hr.employment.register.personal.dependent.index",$employment->key)}}">بازگشت</a>
    @endif
    <button class="btn btn-primary shadow-2 mb-4">بارگذاری</button>

@endsection

