@if(isset($lot_number))
    <a href="#!" data-toggle="collapse" data-target="#lot_{{$lot_number->id}}_{{$id}}"
       aria-expanded="false" aria-controls="lot_{{$lot_number->id}}_{{$id}}">
        <b>{{$lot_number->code}}</b>
    </a>
    @if(isset($amount))

     @include("line_product_station.product.unit_of_measure_type._lot_number")
    @endif

@else
    ***
@endif
