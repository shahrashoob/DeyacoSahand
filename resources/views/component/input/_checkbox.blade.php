<div class="form-group">
    @if(isset($lable) || isset($label))
        <label>{!! $lable??$label??"" !!}</label>
    @endif
    <div class="switch switch-{{isset($class)?$class:"alternative"}}  d-inline m-r-10">
        <input type="checkbox" style="display: inline !important;" {{isset($disabled)?"disabled":""}} id="switch-{{$id}}" name="{{$id}}" {{isset($checked) && $checked?"checked":""}} >
        <label for="switch-{{$id}}" class="cr" style="top:6px!important;"></label>
    </div>

</div>
