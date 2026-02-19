<div class="col-md-12">
    <br/>
    @if($label)
        <b>{{$label}}</b>:
    @endif
    <input
            name="{{$id}}"
            id="{{$id}}_1"
            type="radio"
            {{(($value1??1)===$value)?"checked":""}}
            value="{{$value1??1}}"
    /> {{$label1??"بله"}}

    <input
            name="{{$id}}"
            id="{{$id}}_0"
            type="radio"
            {{$value==($value0??0)?"checked":""}}
            value="{{$value0??0}}"
    /> {{$label0??"خیر"}}

    @if(isset($label2))

        <input
                name="{{$id}}"
                id="{{$id}}_2"
                type="radio"
                {{$value==($value2??0)?"checked":""}}
                value="{{$value2??0}}"
        /> {{$label2}}

    @endif
    <br/>
    <br/>
</div>
