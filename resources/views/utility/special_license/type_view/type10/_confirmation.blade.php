@php $form=$reference;@endphp
@php $boject1=$special_license->getObject1();@endphp
@php
    $product_shrinkage_info=$boject1["product_shrinkage_info"];
    $product_list=$boject1["product_list"];
@endphp
<div class="col-md-12" >

    با توجه به اینکه اینجانب
    <b>{{$special_license->worker->fullname()}}</b>


    قصد دارم   بسته بندی
    <b>{{$reference->code}} </b>

    تغییر دهم و
    درصد جمع شدگی کالا در این بسته بندی
    به علت
    <b>{{$special_license->getDescription()}}</b>
خارج از عرف می باشد،

    خواهشمند است، در صورت صلاحدید با انجام این کار موافقت فرمایید.
</div>
<br/>
<br/>

@include("utility.special_license.type_view.type10._product_shrinkage_info",["product_list"=>$product_list,"product_shrinkage_info"=>$product_shrinkage_info])
