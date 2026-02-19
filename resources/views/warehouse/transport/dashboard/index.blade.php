@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست بسته بندی های ارسال بار
                        @if($dashboard_type!="customer")
                            - درخواست {{$product_request_form->getCode()}}
                        @else
                            -  {{$product_request_form->order->customer->caption}}
                        @endif

                    </h5>

                    <a href="{{route("wh.transport.dashboard.create_transport_item",[$product_request_form,$page,$dashboard_type])}}"
                       class="btn btn-primary">افزودن بسته بندی جدید </a>
                    @if($dashboard_type=="customer")

                        <a href="{{route("wh.out.customer.view",[$product_request_form->order->customer_id])}}?page={{$page}}"
                           class="btn btn-outline-dark">بازگشت</a>
                    @else
                        <a href="{{route("wh.out.dashboard.view",[$product_request_form,$page])}}"
                           class="btn btn-outline-dark">بازگشت</a>
                    @endif
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("wh.transport.dashboard.confirm_multi_transport_item",[$product_request_form,$page])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>
                                        <input id="select_all" type="checkbox">
                                    </th>
                                    @if($dashboard_type=="customer")
                                        <th>شماره درخواست</th>
                                    @endif
                                    <th> بسته بندی حمل و نقل</th>
                                    <th>تعداد بسته بندی</th>
                                    <th>مقدار کل</th>
                                    <th>تاریخ ایجاد</th>
                                    <th>وضعیت</th>
                                    <th></th>
                                </tr>

                                </thead>
                                <tbody>
                                @php
                                    $row=0;
                                    $sum_amount=0;
                                    $show_confirm=false;
                                @endphp
                                @foreach($transport_items as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            @if($item->status_id == 6010001 )
                                                {{--                                                تایید موفقت--}}
                                                <input class="myCheckBox "
                                                       name="data[transport_item][{{$item->id}}]"
                                                       type="checkbox">
                                                @php $show_confirm=true; @endphp
                                            @endif
                                        </td>

                                        @if($dashboard_type=="customer")
                                            <th>{{$item->product_request_form->code}}</th>
                                        @endif
                                        <td>
                                            <a href="{{route("wh.transport.dashboard.packing_list",[$item,$page,$dashboard_type])}}">
                                                {{$item->code()}}
                                            </a>
                                        </td>
                                        <td>
                                            {{$item->transport_packing_list()->count()}}
                                        </td>
                                        <td>
                                            @php
                                            $amount=$item->transport_packing_list_sum_amount();
                                            $sum_amount+=$amount;
                                            @endphp
                                            {{$amount}}
                                        </td>
                                        <td>
                                            {{$item->create_datetime()}}
                                        </td>
                                        <td>{{$item->status->caption}}</td>
                                        <th>
                                            <a href="{{route("wh.transport.dashboard.download",$item)}}">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <a href="{{route("wh.transport.dashboard.print",[$item,60,87])}}">
                                                <i class="fa fa-print"></i>
                                            </a>
                                            @if($item->status_id == 6010001 )
                                                <a href="{{route("wh.transport.dashboard.transport_item_delete",$item)}}"
                                                   class="text-danger"
                                                   onclick="return confirm('آیا از حذف اطمینان دارید؟')">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            @else
                                                <a title="باز کردن بسته بندی حمل و نقل" href="{{route("utility.special_license.panel.new_special_license.index",[5,$item->id,$product_request_form->id])}}"><i class="fa fa-unlock-alt"></i></a>

                                            @endif
                                        </th>

                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4"><b>جمع کل</b></td>
                                    <td><b>{{$sum_amount}}</b></td>
                                    <td colspan="3"></td>

                                </tr>
                                </tbody>

                            </table>
                        </div>
                        @if($show_confirm)
                        <div class="col-md-12 center">
                            <button type="submit" class="btn btn-primary">تبت نهایی بسته بندی ها</button>
                        </div>
                        @endif
                    </form>
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
        $("#select_all").change(function () {

            $('.myCheckBox').each(function () {
                $(this).prop('checked', $("#select_all").is(':checked'));
            });
        });
    </script>
@endsection
