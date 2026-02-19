<div class="md-modal md-effect-{{$id}}" id="modal-{{$id}}" >
    <div class="md-content" style="border: 1px solid #3F4D67;">
        <h3 class="theme-bg{{$theme}}">{{$title}}</h3>
        <div>
            <p id="modal-content-{{$id}}">{!! $content !!}</p>
        </div>
        <div class="">
            <div class="" >
                @if(isset($btn_title))
                    <a id="modal-url-{{$id}}" href="#sdfs" class="btn {{$btn_class}}">{{$btn_title}}</a>
                @endif
                @if(isset($btn_submit))
                    <button id="modal-btn-{{$id}}" class="btn {{$btn_class}}">{{$btn_submit}}</button>
                @endif
                <a class="btn btn-outline-dark  md-close ">بستن </a>
            </div>
        </div>
    </div>
</div>
