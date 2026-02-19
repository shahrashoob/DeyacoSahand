@php $production=$special_license->getObject1();@endphp
@php $replace_production=$special_license->getObject2();@endphp
@php $contractor=$special_license->getObject3();@endphp
<div class="col-md-12">

    با توجه به اینکه بسته بندی
    <b>{{$reference->code}}</b>
    حاوی کالای
    @if($production)
        <b>{{$production->product->caption}}</b>
    @else
        @php $packing_form_item=$reference->items()->first(); @endphp
        <b>{{$packing_form_item->product->caption??""}}</b>
    @endif



    @if($production && $production->parent_production)
    به شماره پیمان
    <b>{{$production->parent_production->serial}}</b>
    و کالای
        <b>{{$production->parent_production->product->caption }}</b>
    تعلق دارد و اکنون
    @else
        دارای کارت پیمان نمی باشد و اکنون

    @endif
    اینجانب
    <b>{{$special_license->worker->fullname()}}</b>
    به دلیل
    <b>{{$special_license->getDescription()}}</b>
    قصد دارم، این بسته بندی را به پیمانکار
    <b>{{$contractor->caption}}</b>
    تخصیص داده و آن را برای شماره پیمان

    <b>{{$replace_production->serial}}</b>
    با کالای
    <b>{{$replace_production->product->caption }}</b>

    ارسال نمایم.
    <br/>
    لذا در صورت صلاحدید تایید فرمایید، این اقدام صورت گیرد.


</div>


