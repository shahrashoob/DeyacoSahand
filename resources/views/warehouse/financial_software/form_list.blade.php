@extends('layouts.admin._master',["keypress_enable"=>1])
@section("page_header_title","داشبورد انبار ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("warehouse.financial_software._search_view",["route"=>"wh.financial_software.form_list"])
        </div>
        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5> برگ های خروج ورود به انبار</h5>

                </div>
                <div class="card-block">

                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>#</th>

                                    <th>کد</th>
                                    <th>تاریخ و ساعت</th>
                                    <th>کاربر ایجاد کننده</th>
                                    <th>انبار</th>
                                    <th> وضعیت(فرم ورود/برگ خروج)</th>
                                    <th> فرم های انتقال</th>
                                    <th>ثبت سند حسابداری</th>
                                    <th>ثبت تراکنش انبار</th>
                                    <th>ثبت فاکتور فروش</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=$list->firstItem();@endphp
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>
                                            {{$item->getCode()}}
                                        </td>
                                        <td>
                                            {{$item->get_create_date_and_time()}}
                                        </td>
                                        <td>
                                            {{$item->worker->fullname()}}
                                        </td>
                                        <td>{{$item->warehouse->caption??""}}</td>
                                        <td>{{$item->status->caption??""}}</td>
{{--                                        <td>{{$item->financial_software_status->caption??""}}</td>--}}
                                        <td>
                                            <a href="{{route("wh.financial_software.transfer_from_list",[$item,$list->currentPage()])}}">
                                               {{$item->financial_software_transfer_form()->count()}}
                                               فرم انتقال
                                            </a>
                                        </td>
                                        <td>
                                            @include("warehouse.financial_software._result",[
	                                                                                "form"=>$item,
                                       	                                            "financial_software_trans_kind_type_id"=>10,
                                                                                   "financial_software_trans_kind_form_id"=>$item->financial_software_trans_kind_form_id,
                                                                                   "status_id"=>$item->accounting_document_status_id])
                                        </td>
                                        <td>
                                            @include("warehouse.financial_software._result",[
	                                                                                "form"=>$item,
                                       	                                            "financial_software_trans_kind_type_id"=>20,
                                                                                   "financial_software_trans_kind_form_id"=>$item->financial_software_trans_kind_form_id,
                                                                                   "status_id"=>$item->accounting_document_status_id])
                                        </td>
                                        <td>
                                            @include("warehouse.financial_software._result",[
	                                                                                "form"=>$item,
                                       	                                            "financial_software_trans_kind_type_id"=>30,
                                                                                   "financial_software_trans_kind_form_id"=>$item->financial_software_trans_kind_form_id,
                                                                                   "status_id"=>$item->accounting_document_status_id])
                                        </td>


                                        {{--                                        <td> @include("warehouse.financial_software._result",[--}}
                                        {{--	                                            "financial_software_trans_kind_type_id"=>10,--}}
                                        {{--	                                            "financial_software_trans_kind_form_id"=>$item->financial_software_trans_kind_form_id,--}}
                                        {{--                                                "status_id"=>$item->accounting_document_status_id]) </td>--}}

                                        {{--                                        <td> @include("warehouse.financial_software._result",[--}}
                                        {{--	                                            "financial_software_trans_kind_type_id"=>20,--}}
                                        {{--	                                            "financial_software_trans_kind_form_id"=>$item->financial_software_trans_kind_form_id,--}}
                                        {{--                                                "status_id"=>$item->warehouse_transaction_status_id]) </td>--}}

                                        {{--                                        <td> @include("warehouse.financial_software._result",[--}}
                                        {{--	                                            "financial_software_trans_kind_type_id"=>30,--}}
                                        {{--	                                            "financial_software_trans_kind_form_id"=>$item->financial_software_trans_kind_form_id,--}}
                                        {{--                                                "status_id"=>$item->sale_invoice_status_id]) </td>--}}

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
                </div>
                <div class="text-center">
                    {{$list->links('pagination::bootstrap-4')}}
                </div>
                <div class="center">
                    <a class="btn btn-outline-dark" href="{{route("wh.financial_software.index")}}">بازگشت</a>
                </div>
            </div>

        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
