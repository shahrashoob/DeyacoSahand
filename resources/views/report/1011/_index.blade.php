<div class="col-xl-6 col-md-6">

    <div class="card">
        <div class="card-header">
            <a href="{{route("report.1011.submit")}}">
                <h5> میزان کالای تولید شده {{$start_jdate." ".$end_jdate}}</h5>
            </a>

        </div>
        <div class="card-block">
            <div id="lineProductChart1011" style="width: 95%; height: 300px; overflow: auto ">
            </div>
        </div>
    </div>
</div>

@include("component.echart.scripts",["data"=>$line_list_1011["amount"]["data"],"series"=>$line_list_1011,"type"=>'multi_line',"id"=>"lineProductChart1011"])
