@php
    $packing_form=$reference;
    $packing_form_item=null;
 @endphp
<div class="col-md-12">

{{--    اینجانب--}}
{{--    <b>{{$special_license->worker->fullname()}}</b>--}}

    با توجه به اینکه
    <b>{{$software_name}}</b>
    وزن خالص کالا(های)
    <b>
    @foreach($packing_form->items()->groupBy("product_id")->get() as $packing_form_item) {{$packing_form_item->product->caption??""}} @endforeach
    </b>
    با شماره بسته بندی
    <b>{{$packing_form->code}}</b>
    به مقدار
    <b>{{round($special_license->param3,3)}} {{$packing_form_item->product->unit->caption??""}}</b>
    را
    <b>{{round($special_license->param1,3)}} کیلوگرم</b>
    محاسبه کرده است
    و اکنون وزن خالص واقعی این بسته بندی
    <b>{{round($special_license->param2,3)}} کیلوگرم</b>
    می باشد، خواهشمند است
    در صورت صلاحدید موافقیت فرمایید تا
    وزن خالص  <b>{{round($special_license->param2,3)}} کیلوگرم</b>
     به عنوان وزن صحیح این بسته بندی درج شود.
    <br/>
    توجه فرمایید که تاثیرات این تغییر وزن را در قیمت محصول لحاظ نمایید.
    <br/>
    با تشکر
    <br/>
    <b>{{$special_license->worker->fullname()}}</b>
    <br/>
    <br/>

</div>
