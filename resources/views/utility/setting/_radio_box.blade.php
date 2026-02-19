<div class="col-md-12">
    <br/>

    <b>{{$values[$key]->caption}}</b>:
    <input
        name="{{ $values[$key]->key}}"
        type="radio"
        {{$values[$key]->integer_value==1?"checked":""}}
        value="1"
    /> {{$label1}}

    <input
        name="{{ $values[$key]->key}}"
        type="radio"
        {{$values[$key]->integer_value==0?"checked":""}}
        value="0"
    />{{$label0}}

    @if(isset($label2))
        <input
                name="{{ $values[$key]->key}}"
                type="radio"
                {{$values[$key]->integer_value==2?"checked":""}}
                value="2"
        />{{$label2}}
    @endif
    <br/>
    <br/>
</div>
