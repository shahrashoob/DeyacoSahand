@php $product_request_form=$reference;@endphp
@php $product_request_form_item=$special_license->getObject1();@endphp
@php $packing_form=$special_license->getObject2();@endphp
@php $warehouse=$special_license->getObject3();@endphp
<div class="col-md-12">

    با توجه به اینکه اینجانب
    <b>{{$special_license->worker->fullname()}}</b>

    به علت
    <b>{{$special_license->getDescription()}}</b>

    قصد دارم کالای
    <b>{{$packing_form->items()->first()->product->caption}} </b>

    با درجه
    <b>{{$packing_form->items()->first()->degree->caption}}</b>

    با بسته بندی
    <b>{{$packing_form->packing_type->caption}}</b>

    موجود در
    <b> {{$warehouse->caption}}</b>

را
    به جای کالای
    <b>{{$product_request_form_item->product->caption??""}} </b>


    در درخواست خروج از انبار
    <b>{{$product_request_form->code}}</b>

    ارسال نمایم.
    <br/>
    خواهشمند است در صورت صلاح دید موافقت فرمایید تا درخواست خروج از انبار

    <b>{{$product_request_form->code}}</b>
    تغییر یابد.
</div>


