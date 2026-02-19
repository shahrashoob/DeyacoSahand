@if(isset($shade_number))
    <a href="#!" data-toggle="collapse" data-target="#lot_{{$shade_number->id}}_{{$id}}"
       aria-expanded="false" aria-controls="lot_{{$shade_number->id}}_{{$id}}">
        <b>{{$shade_number->code}}</b>
    </a>
    @if(isset($amount))
        <b>({{$amount}} متر)</b>
    @endif

@else
    ***
@endif
