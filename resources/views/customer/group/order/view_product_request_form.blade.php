@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان - سفارش   ".$order->code())

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>فرم درخواست کالا از انبار کد {{$product_request_form->code}}</h5>
                </div>
                <div class="card-block">


                    <div class="row">
                        @include("component.input._lable",["label"=>"درخواست دهنده","value"=>$product_request_form->worker->fullname()])
                        @include("component.input._lable",["label"=>"وضعیت","value"=>$product_request_form->status->caption??""])
                        @include("component.input._lable",["label"=>"تاریخ و زمان هماهنگی","value"=>$product_request_form->coordinate_date_time()])

                        <div class="table-responsive">
                            <table class="table table-styling center" style="">
                                <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th>کد کالا</th>
                                    <th> عنوان کالا</th>
                                    <th> واحد سنجش</th>
                                    <th> خط ورودی</th>
                                    <th> مقدار درخواست</th>
                                    <th> مقدار تحویل شده</th>
                                    <th> مقدار باقی مانده</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($product_request_form->items as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>{{$item->product->code}}</td>
                                        <td>{{$item->product->caption}}</td>
                                        <td>{{$item->product->unit->caption}}</td>
                                        <td>{{$item->input_line_code}}</td>
                                        <td>{{$item->amount_request}}</td>
                                        <td>
                                            {{$item->amount_sent}}

                                        </td>
                                        <td>{{$item->amount_remaining}}</td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>


                    </div>
                    <div class="center">
                        <a href="{{route("customer_group.order.show",$order)}}"
                           class="btn btn-outline-dark">بازگشت</a>
                    </div>
                </div>
            </div>
        </div>


    </div>

@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "packing_type_id_auto": "required",
                "degree_id_auto": "required",
                "amount": {
                    required: true,
                    min: 1
                },
                "carrier_code": "required"
            }
        });
    </script>
@endsection
