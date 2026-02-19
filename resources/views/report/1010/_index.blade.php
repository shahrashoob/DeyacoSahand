<div class="col-xl-6 col-md-6">

    <div class="card">
        <div class="card-header">
            <a href="{{route("report.1010.submit")}}">
                <h5> شاخص های بهره وری {{$start_jdate." ".$end_jdate}}</h5>
            </a>

        </div>
        <div class="card-block">
            <div id="BarChart1010" style="width: 95%; height: 300px; overflow: auto ">
            </div>
        </div>
    </div>
</div>


@include("component.echart.scripts",["data"=>$line_list_1010,"type"=>'bar',"id"=>"BarChart1010",])
