@if($value+0 > 100)

    <div class="progress" style="background: red;height: {{$height??6}}px;">
        <div class="progress-bar {{isset($class)?$class:"progress-c-theme2"}}" role="progressbar"
             aria-valuenow="{{(100/$value)*100}}" aria-valuemin="0"
             aria-valuemax="100" style="width: {{(100/$value)*100}}%; height: {{$height??6}}px;"></div>
    </div>
@else
    <div class="progress" style="height: {{$height??6}}px; {{isset($background)?"background:$background":""}}" >
        <div class="progress-bar {{isset($class)?$class:"progress-c-theme2"}}" role="progressbar"
             aria-valuenow="{{$value}}" aria-valuemin="0"
             aria-valuemax="100" style="width: {{$value}}%; height: {{$height??6}}px;"></div>
    </div>
@endif
