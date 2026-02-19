@extends('layouts.admin._master')
@section("page_header_title","کارتابل مالی")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> لیست تراکنش های فاکتور نشده
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>تاریخ تراکنش</th>
                                <th>اقدام کننده</th>
                                <th>مبلغ(ریال)</th>
                                <th>نوع تراکنش</th>
{{--                                <th>کد رهگیری بانک</th>--}}
{{--                                <th>شناسه پرداخت</th>--}}
{{--                                <th>شماره سفارش</th>--}}

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)

                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item-> get_created_at()??""}}</td>
                                    <td>{{$item->worker->fullname()??""}}</td>
                                    <td>{{ number_format($item->amount) ??""}}</td>
                                    @if($item->client_transaction_type_id==2)
                                        <td><a href="{{route("accounting.client.transaction.show",$item)}}">{{$item->client_transaction_type->caption??""}}</a></td>
                                    @else
                                    <td>{{$item->client_transaction_type->caption??""}}</td>
                                    @endif
{{--                                    <td>{{$item->get_track_id()??""}}</td>--}}
{{--                                    <td>{{$item->get_payment_id_in_deyaco()??""}}</td>--}}
{{--                                    <td>{{$item->get_order_id_in_deyaco()??""}}</td>--}}


                                </tr>

                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$list->firstItem()}}</b>
                        تا
                        <b>{{$list->lastItem()}}</b>
                        از
                        <b>{{$list->total()}}</b>
                        رکورد موجود
                    </div>
                </div>
                <div class="text-center">
                    {{$list->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection