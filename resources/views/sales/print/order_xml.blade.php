
<_XPXML PrDocKind="0">
    @foreach($order->orderFactor as $item)
    <_FMainLine Mat="{{$item->product->code}}"  Amount="{{$item->carton * $item->number_in_carton}}" MatSpec="{{$item->number_in_carton}}" PriceOrg="0" PriceKind="0" Price="{{round($item->price/100,2)}}" DirectDiscount="{{round($item->total_off_price/100,2)}}" DirectFare="0" DirectTax="{{round($item->tax_price/100,2)}}" Confirmed="-1" @if($order->customer->order_type_id!=0) TransKind="{{$order->customer->order_type_id== 100 ? 10 : 6}}" @endif />
    @endforeach
</_XPXML>
