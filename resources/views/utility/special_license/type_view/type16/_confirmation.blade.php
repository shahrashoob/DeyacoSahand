@php $entry_datetime=$special_license->getObject1();@endphp
@php $exit_datetime=$special_license->getObject2();@endphp
<div class="col-md-12">

    با توجه به اینکه تردد اینجانب
    <b>{{$special_license->worker->fullname()}}</b>
    در تاریخ
    <b>{{$entry_datetime}}</b>

    وارد سازمان شده و در تاریخ
    <b>{{$exit_datetime}}</b>
    از سازمان خارج شده ام و به علت

    <b>{{$special_license->getDescription()}}</b>
     امکان ثبت ورود و خروج وجود نداشت، خواشمند است موافقت فرمایید
    تا این تردد در سامانه ثبت گردد.
    <br/>



</div>


