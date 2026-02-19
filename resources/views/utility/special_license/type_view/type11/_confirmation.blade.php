@php $machine=$reference;@endphp
@php $boject1=$special_license->getObject1();@endphp
@php
    $product_shrinkage_info=$boject1["product_shrinkage_info"];
    $product_list=$boject1["product_list"];
@endphp
<div class="col-md-12" >

    با توجه به اینکه اینجانب
    <b>{{$special_license->worker->fullname()}}</b>

    به علت
    <b>{{$special_license->getDescription()}}</b>

    قصد دارم یک کانال تولید رزور از نوع
    <b>{{$boject1->production_channel_type->caption}} </b>

    برای ماشین
    <b>{{$machine->caption}} </b>
     ایجاد نمایم.

    خواهشمند است، در صورت صلاحدید با  این مجوز موافقت فرمایید.
</div>
<br/>
<br/>

