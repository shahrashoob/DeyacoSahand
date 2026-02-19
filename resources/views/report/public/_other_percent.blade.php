<div class=" btn-group ">
    <button class="btn btn-outline-dark dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">سایر موارد (  کمتر از {{$percent_chart*100}} درصد  )</button>
    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
        <a href="{{route($route,[.005,$type??""])}}" class="dropdown-item">کمتر از 0.5 درصد</a>
        <a href="{{route($route,[.01,$type??""])}}" class="dropdown-item">کمتر از 1 درصد</a>
        <a href="{{route($route,[.02,$type??""])}}" class="dropdown-item">کمتر از 2 درصد</a>
        <a href="{{route($route,[.04,$type??""])}}" class="dropdown-item">کمتر از 4 درصد</a>
        <a href="{{route($route,[.08,$type??""])}}" class="dropdown-item">کمتر از 8 درصد</a>
        <a href="{{route($route,[.1,$type??""])}}" class="dropdown-item">کمتر از 10 درصد</a>

    </div>
</div>
