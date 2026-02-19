@php $product=$special_license_type->getObject1($param1);@endphp
@php $max_show_packing_form_code_in_special_license=$special_license_type->getObject2($param2);@endphp
<div class="col-md-12">

    اینجانب اطمینان دارم که همه بسته بندی های موجود در بار
    <b>{{$reference->code}}</b>
    بارگیری شده است، خواشمند است
    در صورت صلاح دید موافقت فرمایید تا
    <b>{{$max_show_packing_form_code_in_special_license}}</b>
    بارکد
    بسته بندی از کالای
    <b>{{$product->caption}}</b>
    که در بار موجود است و بارکد آن توسط اپراتور خوانده نشده،
    جهت ثبت به اپراتور نمایش داده شود.
<br/>
<br/>
</div>