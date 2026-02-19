@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  فروش ")

@section('content')


        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>لیست درخواست های  {{ $product->caption }} {{$order_list->order->code()}} برای سفارش </h5>
                    </div>

                    <div class="card-block">

                        <div class="row">


                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-styling center" style="">
                                        <thead>

                                        <tr>
                                            <th>ردیف</th>
                                            <th></th>
                                            <th>شماره درخواست</th>
                                            <th>مقدار درخواست</th>
                                            <th>مقدار ارسال شده</th>
                                            <th>مقدار باقی مانده</th>
                                            <th>مقدار در حال تحویل</th>


                                        </thead>
                                        <tbody>
                                        @php $row=0;$sum_amount_request=0;$sum_amount_sent=0; $sum_amount_remaining=0;$sum_current_delivery=0; $allow_permission=false@endphp
                                        @foreach($list as $item)

                                            <tr>
                                                <td>{{++$row}}</td>
                                                <td>

                                                    @if($item->amount_remaining> 0 )

                                                        <a class="text-danger" href="{{route('sales.product_request_permission.remove_product_request_form_item',[$item->prf_id,$product->id,"default"])}}" onclick="return confirm('در صورت کنسل کردن،  مقدار  مجوز برابر می شود با: \n جمع مقدار ارسال شده  و مقدار در حال تحویل ')">
                                                            <i class="fas fa-level-down-alt"></i>
                                                            <i style="margin-right: -5px" class="fas fa-shopping-basket"></i>
                                                        </a>

                                                    @endif
                                                </td>
                                                <td>
                                                    <a
                                                       href="{{route("sales.dashboard.view_product_request_form",[$item->order->id,$item->prf_id,"back_to_permission",$item->order->id,$product->id])}}">

                                                        {{$item->prf_code}}
                                                    </a>

                                                </td>

                                                <td>{{$item->amount_request}}</td>
                                                <td>{{$item->amount_sent}}</td>
                                                <td>{{$item->amount_remaining}}



                                                </td>

                                                <td>{{isset($list_current_delivery_product_request_form[$item->prf_id])?$list_current_delivery_product_request_form[$item->prf_id]:0}}</td>
                                                @php
                                                    $sum_amount_request+=$item->amount_request;
                                                    $sum_amount_sent+=$item->amount_sent;
                                                    $sum_amount_remaining+=$item->amount_remaining;
                                                    $sum_current_delivery+=(isset($list_current_delivery_product_request_form[$item->prf_id])?$list_current_delivery_product_request_form[$item->prf_id]:0);
                                                @endphp
                                            </tr>
                                        @endforeach

                                        <tr>
                                            <td colspan="3">جمع کل</td>
                                            <td>{{$sum_amount_request}}</td>
                                            <td>{{$sum_amount_sent}}</td>
                                            <td>{{$sum_amount_remaining}}</td>
                                            <td>{{$sum_current_delivery}}</td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div class="float-left">
                                        نمايش رکوردهای
                                        <b>{{$list->firstItem()}}</b>
                                        تا
                                        <b>{{$list->lastItem()}}</b>
                                        از
                                        <b>{{$list->total()}}</b>
                                        رکورد موجود


                                    </div>
                                    <div class="text-center">
                                        {{$list->links('pagination::bootstrap-4')}}
                                    </div>

                                </div>


                            </div>
                            <div class="col-md-12 center">
                                <br/>
                                <a href="{{route("sales.product_request_permission.index",$order_list->order->id)}}"
                                   class="btn btn-outline-dark     " type="button">
                                    بازگشت
                                </a>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection


@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "image_file": "required",
            }
        });

    </script>
@endsection

