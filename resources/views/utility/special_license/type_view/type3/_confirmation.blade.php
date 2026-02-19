@php $entry_datetime=$special_license->getObject1();@endphp
@php $exit_datetime=$special_license->getObject2();@endphp
@php $incorect_date=$special_license->getObject4("Y/m/d");@endphp
@php $incorect_datetime=$special_license->getObject4("H:i:s");@endphp
<div class="col-md-12">

    با توجه به اینکه ساعت
    @if($special_license->param3=="input")
        ورود به سازمان
    @elseif($special_license->param3=="output")
        خروج از سازمان
    @endif
    اینجانب
    <b>{{$special_license->worker->fullname()}}</b>
    در تاریخ
        <b>{{$incorect_date}}</b>
    به علت

    <b>{{$special_license->getDescription()}}</b>

    به اشتباه
    (<b>{{$incorect_datetime}}</b>)

      ثبت شده است، خواهشمند است در صورت صلاحدید موافقت فرمایید تا
    به
    @if($special_license->param3=="input")
        <b>{{$entry_datetime}}</b>
    @elseif($special_license->param3=="output")
        <b>{{$exit_datetime}}</b>
    @endif
    اصلاح گردد.
    <br/>



</div>


