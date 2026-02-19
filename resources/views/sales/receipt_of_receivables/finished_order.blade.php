@extends('layouts.admin._master')

@section('page_header_title',"کارتابل وصول مطالبات "." سفارش:".$order->code())

@section('content')


@include("sales.dashboard2._order_details",["order"=>$order])


@include("sales.dashboard2._order_list",["order"=>$order])


<div class="row">
    <div class="col-md-12" style="text-align: center">




 <a href="{{route("sales.receipt_of_receivables.list")}}" class="btn btn-outline-dark">بازگشت</a>


        <a class="btn btn-info" href="{{route("wh.print.print_rfw_order",[$order,1])}}">
            پرینت درخواست</a>

@if($order->exit_status_id!=460000200)
    <a class="btn btn-primary" href="{{route("sales.receipt_of_receivables.exit_permission",$order)}}" onclick="return confirm('آیاز از ثبت مجوز خروج اطمینان دارید؟')"> ثبت مجوز خروج  </a>
@endif

</div>
</div>
@endsection
