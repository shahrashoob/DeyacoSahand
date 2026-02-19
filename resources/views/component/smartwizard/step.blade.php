<div id="smartwizard" class="sw-main sw-theme-dots">
    <ul class="nav nav-tabs step-anchor">
        @php $step_id=1;@endphp
        @foreach($stepInfo["titles"] as $step)
        <li class="nav-item " id="step-{{$step_id++}}">
            <a   class="nav-link">
                <h5> گام {{$step_id-1}}</h5>
                <p class="m-0">{{$step["title"]}}</p>
            </a></li>
            @endforeach
    </ul>
</div>
<script type="text/javascript">
    $(document).ready(function() {

            for(var i=1;i<{{$active??$stepInfo["step"]}}; i++){
                $("#step-"+i).addClass("done")
            }
            $("#step-{{$active??$stepInfo["step"]}}").addClass("active")


    });
</script>
