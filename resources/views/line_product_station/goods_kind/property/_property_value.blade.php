
@if(count($product->property_permission_value))
    <div class="collapse {{isset($show_collapse)?"show":""}} col-md-12" id="{{$id??"collapseExample"}}">


        <div class="alert " style="border: 1px solid #0b0b0b; border-radius: 10px">

            <div class="row">


                @foreach($product->property_permission_value as $item)


                    @if($item->getValue()!="")
                        <span class="col-md-{{isset($col)?$col:"col-md-3"}}">
                        {{$item->property->caption??""}}:
                            <b>
                                {!! $item->getValue() !!}
                             </b>

                        </span>
                    @endif
                @endforeach
            </div>
        </div>

    </div>

@endif
