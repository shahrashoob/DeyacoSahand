@php $product_request_form=$reference;@endphp
@php $product_request_form_item=$special_license->getObject1();@endphp
@php $packing_type=$special_license->getObject2();@endphp
<div class="col-md-12">

    با توجه به اینکه بسته بندی
    <b>{{$packing_type->fullCaption()}}</b>
 در درخواست خروج از انبار
    <b>{{$product_request_form->code}}</b>
    برای کالای
    <b>{{$product_request_form_item->product->caption}}</b>

    به علت

    <b>{{$special_license->getDescription()}}</b>

    وجود ندارد،

    خواهشمند است در صورت صلاح دید موافقت فرمایید تا این بسته بندی به لیست بسته بندی های مجاز کالا اضافه گردد.

</div>


