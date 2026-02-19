@foreach($list as $item)
    <_InvTrans TransKind="{{$item->nosa_code}}" Stock="{{$item->warehouse->code}}" Mat="{{$item->product->code}}"
               Amount="{{round($item->input+$item->output* $item->product->number_in_carton,3)}}"
               OppKind="{{$item->opp_kind}}"
               Desc="{{$item->getDesc($type_description)}}"
               IC="{{$item->ic}}"/>
@endforeach

