@php $product=$special_license->getObject1();@endphp
<div class="col-md-12">
    نظر به اینکه مقدار کالای
    <b>{{$product->caption}}</b>
    در شماره درخواست
    <b>{{$reference->code}}</b>,
    برابر با
    <b>{{$special_license->param2." ".$product->unit->caption}}</b>
    می باشد و اینجانب
    <b>{{$special_license->worker->fullname()}}</b>
    به علت
    <b>{{$special_license->getDescription()}}</b>
    قصد خروج
    <b>{{$special_license->param3." ".$product->unit->caption}}</b>
    را دارم خواهشمند است در صورت صلاح دید موافقت فرمایید.
    <br/><br/>

</div>

