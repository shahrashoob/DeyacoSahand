<div class="col-md-12">
    با توجه به اینکه امکان ادامه تولید بر روی ماشین

    <b>{{$reference->machine->caption}}</b>
    به علت
    <b>{{$special_license->getDescription()}}</b>
    امکان پذیر نمی باشد، خواهشمند است در صورت صلاحدید
    موافقت فرمایید تا پایان تولید کارت تولید ذکر شده انجام شود.
    <br/>
    @if($special_license->status_id ==6040001)
        @foreach($reference->items as $allocation_item)
            مقدار  کارت {{$allocation_item->production->serial}}:
            <b>{{$allocation_item->allocation_amount." ".$allocation_item->product->unit->caption}}</b>
            <br/>
            مقدار تولید شده:
            <b>{{$special_license->param1." ".$allocation_item->product->unit->caption}}</b>
            <br/>
        @endforeach

    @else
        @foreach($reference->items as $allocation_item)
            کارت تولید
            <b> {{$allocation_item->production->serial}}</b>
            <br/>
            مقدار تولید شده:
            <b>{{$special_license->param1." ".$allocation_item->product->unit->caption}}</b>
            <br/>
        @endforeach
    @endif

    <br/>


    <br/>

</div>

