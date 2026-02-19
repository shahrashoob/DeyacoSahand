@php $form=$reference;@endphp
@php $supplier=$special_license->getObject1();@endphp
<div class="col-md-12">

    با توجه به اینکه اینجانب
    <b>{{$special_license->worker->fullname()}}</b>



    قصد دارم  بخشی یا همه کالاهای داخل فرم ورود به انبار
    <b>{{$reference->code}} </b>




    موجود در
    <b> {{$reference->warehouse->caption}}</b>

    را
    به علت
    <b>{{$special_license->getDescription()}}</b>
    به تامین کننده
    (<b>{{$supplier->caption}}</b>)

    عودت نمایم،
    خواهشمند است، در صورت صلاحدید با  این موضوع موافقت فرمایید.
</div>
