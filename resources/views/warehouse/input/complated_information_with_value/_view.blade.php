
<section >
@php
    if(!isset($data))
        {
            $newData = [];
        }
else {
    $newData = json_decode($data);
}
@endphp
<div class="d-flex flex-column border-top border-left border-right">



    @for($i = 0 ; $i < count($newData) ; $i++ )
       @php  $item = $newData[$i]; @endphp

        <div id="{{$i}}" class="d-flex flex-row border ">
           <div class="font-weight-bold w-25 p-1">
               {{$item->w}}
           </div>
            <div class="font-weight-bold w-25 p-1">
                {{$item->m}}
            </div>
            @if($item->status == 'denied')
            <i class="fa fa-times text-danger  w-25 p-1"></i>

            @else
            <i class="fa fa-check text-success  w-25 p-1"></i>

            @endif
            <div id="test">حذف</div>


        </div>
    @endfor
</div>


</section>

