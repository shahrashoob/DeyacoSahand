@php $transport_item=$reference;@endphp
@php $product_request_form=$transport_item->product_request_form;@endphp
<div class="col-md-12">

    اینجانب
    <b>{{$special_license->worker->fullname()}}</b>

    درخواست بازکردن بسته بندی حمل و نقل
    <b>{{$transport_item->code}}</b>
    که در درخواست خروج از انبار
    <b>{{$product_request_form->code??""}}</b>
    @if($product_request_form->order)
        متعلق به سفارش

        <b>{{$product_request_form->order->code()}} ({{$product_request_form->order->customer->caption}})</b>
    @endif
    ایجاد شده است را به علت

    <b>{{$special_license->getDescription()}}</b>
    دارم، خواشمند است در صورت صلاحدید با باز شدن این بسته بندی حمل و نقل موافقت فرمایید.
</div>
