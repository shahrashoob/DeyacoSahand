<div class="col-xl-6 col-md-6">

    <div class="card">
        <div class="card-header">
            <a href="{{route("report.1009.submit")}}">
                <h5> میزان کالای استخراج شده {{$start_jdate_days." ".$end_jdate_days}}</h5>
            </a>

        </div>
        <div class="card-block">
            <div id="lineProductChart" style="width: 95%; height: 300px; overflow: auto ">
            </div>
        </div>
    </div>
</div>


@include("component.echart.scripts",["data"=>$line_list_1009["list_line_report"]["amount"]["data"],"series"=>$line_list_1009["list_line_report"],"type"=>'multi_line',"id"=>"lineProductChart"])
