<div class="col-xl-12 col-md-12">

    <div class="card">
        <div class="card-header">
            <a href="{{route("report.1013.index")}}">
                <h5> {{$chart_caption}}  </h5>
            </a>

            <div class="card-header-right">

                @include("report.1013._report_btn")

                @if(isset($chart_other_percent))
                    @include("report.public._other_percent",["percent_chart"=>$chart_other_percent,"route"=>"report.1013.set_chart_percent","type"=>$type_percent])
                @endif
            </div>

        </div>
        <div class="card-block">
            <div id="{{$chart_id}}" style="width: 92%; height: 800px; overflow: auto ">
            </div>
        </div>
    </div>
</div>


@include("component.echart.scripts",["data"=>$chart_data["xAxis"]["data"],"series"=>$chart_data["series"],"type"=>'bar_stack',"id"=>$chart_id])
