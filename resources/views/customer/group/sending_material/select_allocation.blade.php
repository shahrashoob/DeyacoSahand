@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")

    <div class="row">

        @include("customer.group.order._order_info")

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>انتخاب درخواست</h5>
                </div>
                <div class="card-block">
                    <div class="alert alert-info">
                        با توجه به اینکه برای این سفارش بیش از یک درخواست برای شما ارسال شده است، لطفا برای ثبت بسته
                        بندی ها یکی از درخواست ها را انتخاب نمایید.
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>کد درخواست (شماره تخصیص)</th>
                                    <th>مقدار درخواست</th>
                                    <th> وضعیت</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($machine_allocations as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            <a href="{{route($route_path,[$order,$item->id])}}">{{$item->allocation_id}}</a>
                                        </td>
                                        <td>{{$item->allocation_amount??""}} {{$item->product->unit->caption}}</td>
                                        <td>{{$item->status->caption}}</td>



                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="w-100"></div>
    <br/>
    <br/>
    <div class="col-md-12" style="text-align: center">
        <a class="btn btn-dark "
           href="{{route("customer_group.order.show",$order)}}">بازگشت</a>

    </div>

@endsection

