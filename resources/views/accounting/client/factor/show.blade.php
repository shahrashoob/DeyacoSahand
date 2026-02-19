@extends('layouts.admin._master')
@section("page_header_title","کارتابل مالی")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        لیست تراکنش های فاکتور به شماره
                        {{$client_factor->code}}
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
                                    <td>{{$item->client_transaction_type->caption??""}}</td>


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
            <a href="{{route("accounting.client.factor.index")}}"
               class="btn btn-outline-dark btn-lg">بازگشت</a>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection