{{--
$data[][
    title,
    active,
    id,
    content
]
--}}
<ul class="nav nav-tabs"  role="tablist">
    @foreach($data as $item)
        <li class="nav-item">
            <a class="nav-link text-uppercase {{isset($item["active"])?$item["active"]:""}} show" id="{{$item["id"]}}-tab" data-toggle="tab" href="#{{$item["id"]}}" role="tab"
               aria-controls="{{$item["id"]}}" aria-selected="{{isset($item["active"])?"true":"false"}}">{{$item["title"]}}}</a>
        </li>
    @endforeach
</ul>

<div class="tab-content" >
    @foreach($data as $item)
    <div class="tab-pane fade {{isset($item["active"])?$item["active"]:""}} show" id="{{$item["id"]}}" role="tabpanel" aria-labelledby="{{$item["id"]}}-tab">
        {!! $item["content"] !!}
    </div>
    @endforeach
</div>
