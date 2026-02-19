<!--  -->
<div class="col-md-6 col-xl-4">
    <div class="card Online-Order">
        <div class="card-block">
            <h5>{{$title}}</h5>
            <h6 class="text-muted d-flex align-items-center justify-content-between m-t-20"><span class="float-right f-18 text-c-purple"> % {{$persent}}</span></h6>
            <div class="progress mt-3">
                <div class="progress-bar progress-c-theme2" role="progressbar" style="width:{{$persent}}%;height:6px;" aria-valuenow="{{$persent}}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <span class="text-muted mt-2 d-block">{{$description}}</span>
        </div>
    </div>
</div>
<!-- / -->
