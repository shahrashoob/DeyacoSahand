@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  فروش "." سفارش:".$order->code())

@section('content')


        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>  مجوز بارگیری برای {{$order->customer->caption}} </h5>
                    </div>

                    <div class="card-block">

                        <div class="row">


                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-styling center" style="">
                                        <thead>

                                        <tr  >

                                            <th>ردیف</th>
                                            <th>نام و کد کالا</th>
                                            <th> واحد سنجش</th>
                                            <th>  سفارش</th>
                                            <th>  مجوز صادر شده</th>
                                            <th style="background: #d8cccc">موجودی کل</th>

                                            <th>  تحویل شده</th>
                                            <th>  باقی مانده</th>
                                            {{--                                        <th> موجودی بسته بندی های مجاز</th>--}}
                                            {{--                                        <th> موجودی سایر بسته بندی ها </th>--}}
                                            {{--                                        <th> موجودی بسته بندی های مجاز در راه </th>--}}
                                            {{--                                        <th> موجودی سایر بسته بندی های  در راه </th>--}}
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @php $row=0;@endphp
                                        @foreach($product_request_form_items as $item)

                                            <tr  >

                                                <td>{{++$row}}</td>

                                                <td>{{$item->product->caption}} <br/>
                                                    {{$item->product->code}}
                                                </td>
                                                <td>{{$item->product->unit->caption}}</td>

                                                <td>{{is_null($item->order_amount)?"":$item->order_amount}}</td>
                                                <td>{{is_null($item->amount_request)?"":$item->amount_request}}</td>

                                                <td>
                                                    {{$inventory_list[$item->product_id]}}
                                                </td>

                                                <td>
                                                    {{round($item->amount_sent,4)}}

                                                </td>
                                                <td>{{is_null($item->amount_remaining)?"":$item->amount_remaining}}</td>


                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>


                            </div>
                            <div class="col-md-12 center">
                                <br/>
                                <a href="{{route("sales.product_request_permission.index",$order)}}" class="btn btn-outline-dark     " type="button">
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
        $(".checkbox_permission").click(function () {
            order_list_id = $(this).data('id');
            if ($(this).is(":checked")) {
                $("#product_permission_" + order_list_id).css("display", "")
            } else {
                $("#product_permission_" + order_list_id).css("display", "none")
            }
        })
    </script>
@endsection

@section("styles")
    <style>


    </style>
@endsection

