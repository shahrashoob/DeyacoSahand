@if(isset($lot_number))
    <a href="#!" data-toggle="collapse" data-target="#lot_{{$lot_number->id}}_{{$id}}"
       aria-expanded="false" aria-controls="lot_{{$lot_number->id}}_{{$id}}">
        <b>{{$lot_number->code}}</b>
    </a>
    @if(isset($amount))
        <b>({{round($amount,2)}} {{$uni_caption??""}})</b>
    @endif

@else
    ***
@endif
