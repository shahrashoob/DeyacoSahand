@extends('layouts.admin._master',["keypress_enable"=>1])
@section("page_header_title","داشبورد انبار ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5> فرم های انتقال {{$form->code}}</h5>

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
                                    <th>وضعیت </th>
                                    <th>ثبت سند حسابداری</th>
                                    <th>ثبت تراکنش انبار</th>
                                    <th>ثبت فاکتور فروش</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1;@endphp
                                @foreach($form->financial_software_transfer_form as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>
                                            {{$item->getCode()}}
                                        </td>
                                        <td>
                                            {{$item->get_create_date_and_time()}}
                                        </td>
                                        <td>{{$item->status->caption??""}}</td>
                                        <td>
                                            @include("warehouse.financial_software._result",[
	                                                                                "form"=>null,
                                       	                                            "financial_software_trans_kind_type_id"=>10,
                                                                                   "financial_software_transfer_form_id"=>$item->id,
                                                                                   "status_id"=>$item->accounting_document_status_id])
                                        </td>
                                        <td>
                                            @include("warehouse.financial_software._result",[
	                                                                                "form"=>null,
                                       	                                            "financial_software_trans_kind_type_id"=>20,
                                                                                   "financial_software_transfer_form_id"=>$item->id,
                                                                                   "status_id"=>$item->warehouse_transaction_status_id])
                                        </td>
                                        <td>
                                            @include("warehouse.financial_software._result",[
	                                                                                "form"=>null,
                                       	                                            "financial_software_trans_kind_type_id"=>30,
                                                                                   "financial_software_transfer_form_id"=>$item->id,
                                                                                   "status_id"=>$item->sale_invoice_status_id])
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>


                    </div>
                </div>

                <div class="center">
                    <a class="btn btn-outline-dark" href="{{route("wh.financial_software.form_list")}}?page={{$page}}">بازگشت</a>
                </div>
            </div>

        </div>

        @php
            $random_form= $form->getRandom();
                 $product_request_form_form= $form->getAllProductRequestFormCodes("first_form_form");
                  $sum_amount     =$form->item()->sum( "amount");
                     $sum_sub_amount = $form->item()->sum( "sub_amount") ;
                     $unit=$form->item->first()->product->unit;
                     $sub_unit=$form->item->first()->product->sub_unit;
                     $caption="فرم"
        @endphp
        @include("warehouse.out.exit_form.qr._list_group_by_products")
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
