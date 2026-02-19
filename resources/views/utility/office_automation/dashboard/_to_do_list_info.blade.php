@include("component.input._lable",["lable"=>"اقدام کننده","value"=>$to_do->worker->fullName(),"class_col"=>"col-sm-12"])
@include("component.input._lable",["lable"=>"پست سازمانی","value"=>$to_do->post->caption,"class_col"=>"col-sm-12"])


<div class="col-md-12">
    <label> کارهای مرتبط:</label>
    @foreach($to_do->work_child as $work)
        <a href="{{route("utility.office_automation.dashboard.view",[$work,$office_automation_work->id])}}">
            {{$work->getCode()??""}} ({{$work->status->caption}})
        </a> ,
    @endforeach
</div>


