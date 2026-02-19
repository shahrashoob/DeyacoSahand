<div class="card-block">

    <div class="row">
        <div class="col-md-12">
            <div class="row">
                @foreach($employment->worker->user_academic_degrees as $item)
                    @include("component.input._lable",["id"=>"academic_degree_type_id","label"=>"میزان تحصیلات","value"=>$item->academic_degree_type->caption??""])
                    @include("component.input._lable",["id"=>"feild_of_academic_degree","label"=>"رشته تحصیلی","value"=>$item->feild_of_academic_degree??""])
                    @include("component.input._lable",["id"=>"name_of_academic_degree","label"=>"نام دانشگاه","value"=>$item->name_of_academic_degree??""])
                    @include("component.input._lable",["id"=>"average","label"=>"معدل","value"=>$item->average??""])
                    @if($item->academic_degree_file !=null)
                        @include("component.input._lable",["id"=>"academic_degree_file_id",
                                                                "url"=> route('hr.employment.register.download',[ $item->id,$item->academic_degree_file_id]),
                                                                "label"=>"مدرک تحصیلی",
                                                                "value"=>$item->academic_degree_file->caption??""
                                                                ])
                    @endif

                @endforeach
            </div>
        </div>
    </div>
</div>
{{--<td>--}}
{{--    @if($item->educational_text_file_id)--}}
{{--        <a href="{{ route('hr.employment.register.download',[ $item->id,$item->academic_degree_file_id])}}">{{ $item->educational_text_file->caption??" "}}</a>--}}
{{--    @endif--}}
{{--</td>--}}
