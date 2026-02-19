
<div class="col-md-6 offset-md-6" id="{{$id}}_radio_section">
    <div class="form-group">
        <label class="form-label">{{$lable??""}} {{$label??""}}</label>
        <div class="custom-controls-stacked">
            @foreach($radios as $item)
            <label class="custom-control custom-radio" style="display:inline-block; margin:0 15px">
                <input  name="{{$id}}" type="radio" value="{{$item["value"]}}" class="custom-control-input" aria-invalid="false" @if(isset($item["checked"]) && $item["checked"]) checked @endif>
                <span class="custom-control-label">{{$item["label"]}}</span>
            </label>
            @endforeach
            <br/>
                <label id="{{$id}}" class="error jquery-validation-error small form-text invalid-feedback" for="{{$id}}_" style="display: inline;"></label></div>

    </div>
</div>
