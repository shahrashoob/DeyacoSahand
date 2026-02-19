@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")

    @include("component.input._select", [
       "id"=>"academic_degree_type_id",
       "label"=>"دوره تحصیلات",
       "option"=>$academic_degree_type_option["items"],
       "val"=>$academic_degree_type_option["value"],
       "text"=>$academic_degree_type_option["text"],
       "class_col"=>"",
        "mark"=>"*"
       ])
    <br/>
    @include("component.input._aotocomplet2", [
     "id"=>"feild_of_academic_degree_id",
     "label"=>"رشته تحصیلی",
     "option"=>$feild_of_academic_degree_option["items"],
     "val"=>$feild_of_academic_degree_option["value"],
     "text"=>$feild_of_academic_degree_option["text"],
     "class_col"=>"",
     "mark"=>"*"
    ])

    @include("component.input._text", ["id"=>"name_of_academic_degree", 'label'=>"نام موسسه/دانشگاه",    "value"=> $request["name_of_academic_degree"]??"", "class_col"=>"","mark"=>"*"])

    @include("component.input._number", ["id"=>"average", 'label'=>"معدل","value"=>$request["average"]??"" , "class_col"=>"","mark"=>"*"])


    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", ["id"=>"start_date", 'max_date'=>'today','label'=>"تاریخ شروع ", "value"=>$request["start_date"]??"",  "class_col"=>"","mark"=>"*"])

    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", ["id"=>"end_date", 'label'=>"تاریخ پایان ", "value"=>$request["end_date"]??"",  "class_col"=>"","mark"=>"*"])

    @if($post_document_receive_step_confirm)
        <p class="alert-warning">تحویل مدارک زیر به بایگانی الزامی می باشد.لطفا اصل مدارک را در زمان تحویل به همراه داشته باشید.</p>
    @endif

    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_receive_step_document_type_list])

    <button class="btn btn-primary shadow-2 mb-4">ثبت</button>



    <script>
        document.getElementById('academic_degree_type_id').addEventListener('change', function () {
            var academic_degree = this.value;
            $("#feild_of_academic_degree_id_auto").attr("readonly", true);
            $("#name_of_academic_degree").attr("readonly", true);
            $("#average").attr("readonly", true);
            window.location.href = "{{route('hr.employment.register.personal.academic_degree.index',$employment->key)}}/" + academic_degree;
        });
    </script>

@endsection

