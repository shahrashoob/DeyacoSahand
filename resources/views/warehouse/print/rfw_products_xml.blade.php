<_XPXML>
    @foreach($list as $item)
        <_InvTrans TransKind="8" Stock="{{$item->warehouse->code}}" Mat="{{$item->product->code}}"
                   Amount="{{$item->output}}" OppKind="1"
                   Desc="تحویل کالای درخواستی به سریال تولید {{$item->rfw_form->production->serial??""}}"
                   IC="{{$item->ic}}"/>
    @endforeach
</_XPXML>
