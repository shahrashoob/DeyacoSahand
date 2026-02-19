@foreach($product->getOffers($customer) as $offer)
    @if($offer->percent_off !=0)
        <p class="m-0"><span class="text-c-green" style="color: #EE5757">تخفیف درصدی: {{$offer->min_buy}}-{{$offer->max_buy}} {{$product->unit->bach_caption}} ({{$offer->degree->caption}}) {{$offer->percent_off}}% تخفیف</span>
        </p>
    @endif

    @if($offer->percent_free)
        <p class="m-0"><span class="text-c-green">تخفیف حجمی: {{$offer->min_buy}}-{{$offer->max_buy}}  {{$product->unit->bach_caption}} ({{$offer->degree->caption}}),
                                                            {{$offer->percent_free}}
                                                            %
                                                             {{$offer->product_free->caption??""}}
                                                                ({{$offer->degree_free->caption}})
                                                            رایگان


                                                        </span>
        </p>

    @endif
@endforeach

