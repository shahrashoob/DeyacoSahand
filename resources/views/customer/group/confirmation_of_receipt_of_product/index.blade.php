@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان - سفارش  ".$order->code())

@section('content')


    <form id="form1" autocomplete="off"
          action="{{route("customer_group.confirmation_of_receipt_of_product.confirm_exist_form",[$order,$form])}}"
          method="post"
          novalidate="novalidate">
        @csrf

        @if($checking_carrier_at_delivery_of_product_customer)
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>تایید دریافت کالا</h5>
                        </div>
                        <div class="card-block">
                            <table class="table table-styling center" style=" width: 400px; margin: auto">
                                <tr>
                                    <td colspan="4">
                                        @if($checking_carrier_at_delivery_of_product_customer)
                                            <div class="alert alert-info">
                                                لطفا شماره بسته بندی یا کد حامل هر بسته بندی را در کادر(های) زیر
                                                وارد
                                                نمایید،
                                                ترتیب ورود اهمیتی ندارد
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                لطفا شماره بسته بندی یا کد حامل هر بسته بندی زیر را کنترل نمایید و
                                                در صورت عدم مغایرت، فرم را تایید نمایید
                                                <br/>
                                                مواردی که تیک تایید داشته باشند، ثبت خواهند شد.
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        @if(!$checking_carrier_at_delivery_of_product_customer)
                                            <input type="checkbox" id="check_all" value="false">
                                            همه موارد
                                        @endif
                                    </th>

                                    <th>ردیف</th>
                                    <th> شماره حامل</th>
                                    <th> کد بسته بندی</th>
                                </tr>
                                @php $row=0;$default_focus_set=false;@endphp
                                @foreach($packing_form_list as $item)
                                    <tr>
                                        @if(!$checking_carrier_at_delivery_of_product_customer && $item->status_id!=7007014)
                                            <td>
                                                <input type="checkbox" class="check_box"
                                                       name="data[check_box][{{$item->getCodeNumber()}}]">
                                            </td>
                                        @else
                                            <td class="text-success">
                                                <i class="fa fa-check "></i>
                                            </td>
                                        @endif
                                        <td>بسته {{++$row}}</td>
                                        <td>
                                            @if($item->status_id==7007014 || !$checking_carrier_at_delivery_of_product_customer)
                                                <input class="carrier" id="carrier_{{$row}}"
                                                       data-next_id="{{$row+1}}" data-maxlength="4" type="text"
                                                       style="width: 140px"

                                                       value="{{$item->carrier->code??""}}" disabled>
                                                <input type="hidden" name="data[carrier_code][{{$row}}]"
                                                       value="{{$item->carrier->code??""}}">
                                            @else

                                                <input class="carrier" id="carrier_{{$row}}"
                                                       data-next_id="{{$row+1}}" data-maxlength="4" type="text"
                                                       style="width: 140px"
                                                       name="data[carrier_code][{{$row}}]"
                                                       value="">

                                            @endif
                                        </td>
                                        <td>
                                            @if($item->status_id==7007014 || !$checking_carrier_at_delivery_of_product_customer)
                                                <input class="packing" id="packing_{{$row}}"
                                                       data-next_id="{{$row+1}}"
                                                       data-maxlength="4" type="text" style="width: 140px"

                                                       value="{{$item->getCodeNumber()}}"
                                                       disabled>
                                                /DCPK

                                                <input type="hidden" name="data[packing_form_code][{{$row}}]"
                                                       value="{{$item->getCodeNumber()}}">
                                            @else

                                                <input class="packing" id="packing_{{$row}}"
                                                       data-next_id="{{$row+1}}"
                                                       data-maxlength="4" type="text" style="width: 140px"
                                                       {{$default_focus_set?"":"autofocus"}}
                                                       name="data[packing_form_code][{{$row}}]">
                                                /DCPK
                                                @php $default_focus_set=true;@endphp
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                            </table>

                        </div>
                    </div>
                </div>
                @else
                    @include("warehouse.out.exit_form.qr._index")
                @endif


                <div class="col-md-12 center">

                    <br/>
                    <a class="btn btn-outline-dark"
                       href="{{route("customer_group.order.show",$order)}}">بارگشت</a>
                    @if($form->status_id == 500000500)
                        <button type="submit" class="btn btn-success">تایید دریافت محموله</button>
                    @endif
                </div>

            </div>
    </form>




@endsection
@section("styles")

    @include("component.input.datepicker._script")
    <style>
        .form-group {
            margin: 0px !important;
        }

        .form-control {
            width: 150px !important;
            margin: auto;
        }
    </style>
@endsection
@section("scripts")
    <script>
        $("#check_all").click(function () {
            $(".check_box").prop('checked', $("#check_all").is(':checked'));
        })
        $('#form1').validate({
            rules: {
                "unit_id": "required",
            }
        });
    </script>
@endsection
