@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")



    @include("component.input._lable", ["label"=>" دوره تحصیلی",  "value"=>$user_academic_degree->academic_degree_type->caption,  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"رشته تحصیلی",  "value"=>$user_academic_degree->feild_of_academic_degree->caption,  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"نام موسسه",  "value"=>$user_academic_degree->name_of_academic_degree,  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>" معدل",  "value"=>$user_academic_degree->average,  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"تاریخ شروع",  "value"=>$user_academic_degree->get_start_date(),  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"تاریخ پایان",  "value"=>$user_academic_degree->get_end_date(),  "class_col"=>"col-md-12" ])

    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_receive_step_document_type_list])


        <a class="btn  mb-4" href="{{route("hr.employment.register.personal.academic_degree.index",$employment->key)}}">بازگشت</a>

    <button class="btn btn-primary shadow-2 mb-4">بارگذاری </button>


@endsection

