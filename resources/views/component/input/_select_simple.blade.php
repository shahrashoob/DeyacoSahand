<select
    class="{{isset($class)?$class:"form-control"}}"
    id="{{$id}}"
    name="{{$id}}"
    {{isset($required)?"required":""}}
    {{isset($disabled)?"disabled":""}}
    {{isset($readonly) && $readonly?"readonly":""}}
    {{isset($style)?"style=".$style:""}}
    {{isset($multiple)?"multiple=".$multiple:""}}
    {{isset($required)?"required":""}}
>
    @if(isset($placeholder))
        <option value="">{{$placeholder}}.</option>
    @endif

    @if(isset($option))
        @foreach($option as $item)
            <option
                value="{{($item["value"]===0?"":$item["value"])}}" {{isset($item["selected"])?"selected":""}}>{{$item['caption']??$item['text']??"***"}}</option>
        @endforeach
    @endif
</select>

