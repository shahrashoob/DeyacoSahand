@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')

    <form id="form1" action="{{route("utility.financial_software.setting.submit_warehouse",[$warehouse,1])}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf

        <div class="row">

            <div class="col-md-12">
                <div class="card" style="overflow: auto">
                    <div class="card-header">
                        <h5>تنظیمات ثبت تراکنش {{$warehouse->caption}} در نرم افزار مالی (نوسا)
                        </h5>
                    </div>
                    <div class="card-block">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead>
                            <tr>
                                <th>کد</th>
                                <th>نوع رخداد</th>
                                <th> ورود/خروج</th>

                                <th>ثبت تراکنش به صورت تجمیعی</th>
                                <th></th>
                                <th></th>
                                <th></th>

                                <th>کد نوع برگه انبار
                                    <br/>
                                    InvKindCode
                                </th>
                                <th>سری
                                    <br/>
                                    InvSeries
                                </th>
                                <th>کد بخش
                                    <br/>
                                    DeptCode
                                </th>
                                <th>کد شرایط تحویل
                                    <br/>
                                    DeliveryCondCode
                                </th>
                                <th>کد نحوه پرداخت
                                    <br/>
                                    PaymentMethodCode
                                </th>
                                <th>کد مرکز فروش
                                    <br/>
                                    SalesCenterCode
                                </th>
                                <th>طرف بدهکار
                                    <br/>
                                    DebSide
                                </th>
                                <th>وضعیت محاسبه کسورو
                                    <br/>
                                    اضافات و پورسانت ها
                                    <br/>
                                    CalcState
                                </th>
                            </tr>

                            </thead>
                            @foreach($trans_kind_list as $trans_kind)
                                <tr>
                                    <td>{{$trans_kind->id}}</td>

                                    <td>
                                        {{$trans_kind->caption}}
                                    </td>
                                    <td>
                                        {{$trans_kind->entry_type->caption}}
                                    </td>

                                    <td>

                                        <input name="has_group_by_product[{{$trans_kind->id}}]" type="checkbox"
                                               @if(isset($financial_software_trans_kind[$trans_kind->id]["has_group_by_product"]) && $financial_software_trans_kind[$trans_kind->id]["has_group_by_product"])
                                                   checked
                                            @endif
                                        > ثبت تراکنش به صورت تجمیعی
                                        (تراکنش های انبار)

                                    </td>
                                    <td>
                                        @if($trans_kind->has_accounting_document)
                                            <input name="has_accounting_document[{{$trans_kind->id}}]" type="checkbox"
                                                   @if(isset($financial_software_trans_kind[$trans_kind->id]["has_accounting_document"]) && $financial_software_trans_kind[$trans_kind->id]["has_accounting_document"])
                                                       checked
                                                @endif
                                            > ثبت سند حسابداری
                                        @endif
                                    </td>
                                    <td>
                                        @if($trans_kind->has_warehouse_transaction)
                                            <input name="has_warehouse_transaction[{{$trans_kind->id}}]"
                                                   type="checkbox"
                                                   @if(isset($financial_software_trans_kind[$trans_kind->id]["has_warehouse_transaction"]) && $financial_software_trans_kind[$trans_kind->id]["has_warehouse_transaction"])
                                                       checked
                                                @endif
                                            > ثبت تراکنش انبار
                                        @endif
                                    </td>
                                    <td>
                                        @if($trans_kind->has_sale_invoice)
                                            <input name="has_sale_invoice[{{$trans_kind->id}}]" type="checkbox"
                                                   @if(isset($financial_software_trans_kind[$trans_kind->id]["has_sale_invoice"]) && $financial_software_trans_kind[$trans_kind->id]["has_sale_invoice"])
                                                       checked
                                                @endif
                                            > ثبت
                                            فاکتور فروش
                                        @endif
                                    </td>
                                    <td>
                                        <input type="number"
                                               @if(isset($financial_software_trans_kind[$trans_kind->id]["inv_kind_code"]))
                                                   value="{{$financial_software_trans_kind[$trans_kind->id]->inv_kind_code}}"
                                               @endif
                                               style="width: 60px"
                                               name="inv_kind_code[{{$trans_kind->id}}]"
                                        >
                                    </td>
                                    <td>
                                        <input type="number"
                                               @if(isset($financial_software_trans_kind[$trans_kind->id]["inv_series"]))
                                                   value="{{$financial_software_trans_kind[$trans_kind->id]->inv_series}}"
                                               @endif
                                               style="width: 60px"
                                               name="inv_series[{{$trans_kind->id}}]"
                                        >
                                    </td>
                                    <td>
                                        <input type="number"
                                               @if(isset($financial_software_trans_kind[$trans_kind->id]["dept_code"]))
                                                   value="{{$financial_software_trans_kind[$trans_kind->id]->dept_code}}"
                                               @endif
                                               style="width: 60px"
                                               name="dept_code[{{$trans_kind->id}}]"
                                        >
                                    </td>
                                    <td>
                                        <input type="number"
                                               @if(isset($financial_software_trans_kind[$trans_kind->id]["delivery_cond_code"]))
                                                   value="{{$financial_software_trans_kind[$trans_kind->id]->delivery_cond_code}}"
                                               @endif
                                               style="width: 60px"
                                               name="delivery_cond_code[{{$trans_kind->id}}]"
                                        >
                                    </td>
                                    <td>
                                        <input type="number"
                                               @if(isset($financial_software_trans_kind[$trans_kind->id]["payment_method_code"]))
                                                   value="{{$financial_software_trans_kind[$trans_kind->id]->payment_method_code}}"
                                               @endif
                                               style="width: 60px"
                                               name="payment_method_code[{{$trans_kind->id}}]"
                                        >
                                    </td>
                                    <td>
                                        <input type="number"
                                               @if(isset($financial_software_trans_kind[$trans_kind->id]["sales_center_code"]))
                                                   value="{{$financial_software_trans_kind[$trans_kind->id]->sales_center_code}}"
                                               @endif
                                               style="width: 60px"
                                               name="sales_center_code[{{$trans_kind->id}}]"
                                        >
                                    </td>
                                    <td>
                                        <input type="number"
                                               @if(isset($financial_software_trans_kind[$trans_kind->id]["deb_side"]))
                                                   value="{{$financial_software_trans_kind[$trans_kind->id]->deb_side}}"
                                               @endif
                                               style="width: 60px"
                                               name="deb_side[{{$trans_kind->id}}]"
                                        >
                                    </td>
                                    <td>
                                        <input type="number"
                                               @if(isset($financial_software_trans_kind[$trans_kind->id]["calc_state"]))
                                                   value="{{$financial_software_trans_kind[$trans_kind->id]->calc_state}}"
                                               @endif
                                               style="width: 60px"
                                               name="calc_state[{{$trans_kind->id}}]"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>

        </div>


        <a href="{{route("utility.financial_software.setting.index")}}" class="btn btn-outline-dark">بازگشت</a>

        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

    </form>

@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "code": "required",
            }
        });
    </script>
@endsection
