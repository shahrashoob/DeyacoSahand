@php $form=$special_license->getObject1();@endphp
@php $day_number=$special_license->getObject2(); @endphp
@php $max_datetime=$special_license->getObject3(); @endphp
<div class="col-md-12">

    اینجانب قصد دارم بخشی از کالاهای موجود در برگ خروج
    <b>{{$form->code}}</b>
    مربوط به سفارش شماره

    <b>
        {{$reference->code()}}
        ({{$reference->customer->caption}})
    </b>
    را پس از
    <b>{{$day_number}} روز </b>
     از دریافت کالا مرجوع نمایم.
    (در حالی که موعد قانونی
    <b>{{$reference->customer->the_max_day_for_reject_product}} روز</b>
    می باشد)
    خواشمند است در صورت صلاحدید با مرجوع نمودن کالا موافقت فرمایید

    <br/>
</div>
