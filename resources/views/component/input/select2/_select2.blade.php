<div class="{{isset($class_col)?$class_col:"col-md-6 offset-md-6"}}">
    <div class="form-group">

        <label class="form-label">{{$lable??$label??""}}</label>
        @if(isset($link))
            <a href="{{$link["url"]}}"><i class="fa fa-{{$link['icon']}}"></i> {{$link["caption"]}}</a>
        @endif
        <select class="{{isset($class)?$class:"form-control"}}" id="{{$id}}" multiple="multiple"
                name="{{$id}}[]" {{isset($required)?"required":""}}>
            @if(isset($placeholder))
                <option value="">{{$placeholder}}.</option>
            @endif

            @if(isset($option))
                @foreach($option as $item)

                    <option value="{{($item["value"]==0?"":$item["value"])}}"
                            data-select2-id="{{$id}}_{{($item["value"]==0?"":$item["value"])}}" {{isset($item["selected"])?"selected":""}}>{{$item['caption']??$item['text']??"***"}}</option>
                @endforeach
            @endif
        </select>


    </div>
</div>

<script>
    $('#{{$id}}').select2();
</script>
