@php $product=$special_license->getObject1();@endphp
@php $count_show_packing_form_code_in_special_license=$special_license->getObject2();@endphp
<div class="col-md-12">


    با توجه به اینکه اینجانب
    <b>{{$special_license->worker->fullname()}}</b>

    به علت
    <b>{{$special_license->getDescription()}}</b>

    قصد دارم
    <b> {{$count_show_packing_form_code_in_special_license}}</b>
    بسته بندی را بدون خواندن بارکد آنها در بارگیری تایید نمایم، خواهشمند است
    در صورت صلاح دید موافقت فرمایید تا
    <b> {{$count_show_packing_form_code_in_special_license}}</b>
    بارکد
    بسته بندی از کالای
    <b>{{$product->caption}}</b>
    که در بار موجود است و بارکد آن توسط اپراتور خوانده نشده است را
    جهت ثبت به اپراتور نمایش داده شود.

</div>
