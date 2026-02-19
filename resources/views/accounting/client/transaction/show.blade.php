@extends('layouts.admin._master')
@section("page_header_title","کارتابل مالی")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        لیست پیامک های تراکنش
                        به شماره ی
                        {{$client_transaction->id}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>تاریخ تراکنش</th>
                                <th>هزینه پیامک(ریال)</th>
                                <th>وضعیت ارسال</th>
                                <th>شماره فرستنده</th>
                                <th>شماره دریافت کننده</th>
                                <th>نام دریافت کننده</th>
                                <th>کد رهگیری</th>
                                <th>نام پیامک</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)

                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item-> get_created_at()??""}}</td>
                                    <td>{{number_format($item->cost_with_coefficient)??""}}</td>
                                    <td>{{$item->statustext??""}}</td>
                                    <td>{{$item->sender??""}}</td>
                                    <td>{{$item->receptor??""}}</td>
                                    <td>{{isset($worker_list[ltrim($item->receptor,"0")])?$worker_list[ltrim($item->receptor,"0")]->fullname():""}}</td>
                                    <td>{{$item->messageid??""}}</td>
                                    <td>{{$item->sms_template->caption??""}}</td>
                                </tr>
                                <tr>
                                    <td class="text-primary" colspan="8" style="font-size: 11px">
                                        {{$item->sms_template->text??""}}
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
            <a href="{{route("accounting.client.transaction.index")}}"
               class="btn btn-outline-dark btn-lg">بازگشت</a>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection