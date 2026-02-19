@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  فروش ")

@section('content')


        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>لیست سایر سفارشات مشتری - {{ $product->caption }} </h5>

                    </div>

                    <div class="card-block">

                        <div class="row">


                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-styling center" style="">
                                        <thead>

                                        <tr>
                                            <th>ردیف</th>
                                            <th>شماره سفارش</th>
                                            <th> نام مشتری</th>
                                            <th>مقدار سفارش</th>


                                        </thead>
                                        <tbody>
                                        @php $row=0;$sum_amount_request=0;$sum_amount_sent=0; $sum_amount_remaining=0;$sum_current_delivery=0; $allow_permission=false@endphp
                                        @foreach($list as $item)

                                            <tr>
                                                <td>{{++$row}}</td>

                                                <td>
                                                    <a  href="{{route("sales.dashboard.view_order",[$item->order_id])}}">
                                                        {{$item->code()}}
                                                    </a>

                                                </td>
                                                <td>{{$item->customer->caption??""}}</td>
                                                <td>{{$item->carton}}</td>

     @php
                                                    $sum_amount_request+=$item->carton;

                                                @endphp
                                            </tr>
                                        @endforeach

                                        <tr>
                                            <td colspan="3">جمع کل</td>
                                            <td>{{$sum_amount_request}}</td>
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
                                <a href="{{route("sales.product_request_permission.index",$order)}}"
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

