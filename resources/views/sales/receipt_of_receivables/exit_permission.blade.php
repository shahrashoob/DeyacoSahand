@extends('layouts.admin._master')

@section('page_header_title',"کارتابل جاری فروش "." سفارش:".$order->code())

@section('content')


@include("sales.dashboard2._order_details",["order"=>$order])



<div class="row">
    <div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5>ثبت مجوز خروج </h5>
        </div>
        <div class="card-block">



 <form id="form1" style="display: inline" action="{{route("sales.receipt_of_receivables.exit_permission_store",$order)}}" method="post" novalidate="novalidate">
    @csrf


    @include("component.input.datepicker._datepicker",["id"=>"exit_date","lable"=>" تاریخ مجوز خروج "])



    <a href="{{route("sales.receipt_of_receivables.finished_order",$order)}}" class="btn btn-outline-dark">بازگشت</a>

    <button type="submit" class="btn btn-primary" onclick="return confirm('آیا از ثبت مجوز خروج اطمینان دارید؟')"> ثبت مجوز خروج  </button>

</form>


</div>
    </div>
</div>
</div>
@endsection
@section("styles")
@include("component.input.datepicker._script")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection


@section("scripts")
<script>
$('#form1').validate({
            rules: {
                "exit_date":"required",
            }
});
</script>
@endsection
