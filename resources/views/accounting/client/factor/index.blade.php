@extends('layouts.admin._master')
@section("page_header_title","کارتابل مالی")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> لیست فاکتور ها
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>شماره</th>
                                <th>نوع</th>
                                <th>تاریخ</th>
                                <th>مبلغ کل (ریال)</th>
                                <th>دانلود</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)

                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->code??""}}</td>
                                    <td>
{{--                                    <a href="{{route("accounting.client.factor.show",$item)}}">{{$item->client_factor_type->caption??""}}</a>--}}
{{$item->client_factor_type->caption??""}}
                                    </td>
                                    <td>{{$item->get_created_at()}}</td>
                                    <td>{{number_format($item->total_amount)}}</td>
                                    <td>
                                        <a href="{{route('accounting.client.factor.print',$item)}}">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    </td>
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
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> لیست تراکنش های افزایش اعتبار
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
                                <th>کد رهگیری بانک</th>
                                <th>شناسه پرداخت</th>
                                <th>شماره سفارش</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list2 as $item)

                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item-> get_created_at()??""}}</td>
                                    <td>{{$item->worker->fullname()??""}}</td>
                                    <td>{{ number_format($item->amount) ??""}}</td>

                                    <td>{{$item->client_transaction_type->caption??""}}</td>

                                    <td>{{$item->get_track_id()??""}}</td>
                                    <td>{{$item->get_payment_id_in_deyaco()??""}}</td>
                                    <td>{{$item->get_order_id_in_deyaco()??""}}</td>
                                </tr>

                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$list2->firstItem()}}</b>
                        تا
                        <b>{{$list2->lastItem()}}</b>
                        از
                        <b>{{$list2->total()}}</b>
                        رکورد موجود
                    </div>
                </div>
                <div class="text-center">
                    {{$list2->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection