@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")
    <div class="row">


        <div class="col-sm-12 ">
            @include("customer.group.buy._search_view",["route"=>"customer_group.buy.index_product"])
        </div>
        <div class="col-sm-12 ">

            <div class="card">
                <div class="card-header">
                    <h5> لیست محصولات (خدمات)
                        @if($goods_kind_property_value)
                            {{$goods_kind_property_value->product->goods_kind->caption}}
                            -
                            {{$goods_kind_property_value->property->caption}}
                            @if($goods_kind_property_value->property->field_type_id ==3)
                                {{$goods_kind_property_option_value}}
                            @else
                                {{$goods_kind_property_value->value}}
                            @endif

                        @endif
                    </h5>
                    <div class="card-header-right">

                        <a href="{{route("customer_group.buy.shopping_cart",$order)}}">
                            <i class="fa fa-shopping-cart fa-2x  label label-success"> <span
                                        class="label text-white f-24  "
                                        style="font-family: IRANSans">{{$order->orderList->count()}}</span></i>
                        </a>
                    </div>

                </div>
                <div class="card-block pb-0">

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="center">ردیف</th>
                                {{--                                @if($setting["show_product_image"]->integer_value==1)--}}
                                {{--                                    <th></th>--}}
                                {{--                                @endif--}}
                                <th>مشخصات کالا (خدمت)</th>
                                <th class="center">واحد</th>
                                <th class="center">
                                    مبلغ واحد
                                    {{--                                    @if($customer->price_displayed_to_customer_with_tax)--}}
                                    {{--                                            (با احتصاب ارزش افزوده)--}}
                                    {{--                                    @else--}}
                                    {{--                                         (بدون احتصاب ارزش افزوده)--}}
                                    {{--                                    @endif--}}

                                </th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td class="center">{{$row++}}</td>
                                    {{--                                    @if($setting["show_product_image"]->integer_value==1)--}}
                                    {{--                                        <td>--}}
                                    {{--                                            <a href="{{route("customer_group.buy.show_shopping_product",[$order,$customer,$item])}}">--}}
                                    {{--                                                <img class="rounded-circle" style="width: 150px;height: 150px;"--}}
                                    {{--                                                     src="{{asset("product_image/".$item->product->filename())}}"--}}
                                    {{--                                                     onerror="this.onerror=null;this.src='{{url("product_image/product.png")}}';"--}}
                                    {{--                                                     alt="activity-user">--}}
                                    {{--                                            </a>--}}
                                    {{--                                        </td>--}}
                                    {{--                                    @endif--}}
                                    <td>
                                        <a href="{{route("customer_group.buy.show_shopping_product",[$order,$customer,$item->product_id])}}">
                                            <h5 class="mb-1">

                                                {{$item->getCaption()}}
                                            </h5>
                                            <p class="m-0">کد کالا: {{$item->product->code}}
                                                @if($item->service_id)
                                                    &nbsp;
                                                    &nbsp;
                                                    کد خدمت: {{$item->service->code??""}} </p>
                                            @endif
                                            @include("customer.group._product_offer",["product"=>$item->product])

                                        </a>

                                    </td>
                                    <td class="center">{{$item->product->unit->bach_caption}}   </td>
                                    <td class="center">
                                        @php
                                            $tariff_product_master_degree=$item->product->getProductTariffWithMasterDegree($item->tariff_id);
                                            $price=$tariff_product_master_degree->fea;
											if($customer->price_displayed_to_customer_with_tax){
												$price=$price*(1+($tariff_product_master_degree->tax + $tariff_product_master_degree->fare)/100);
											}
                                        @endphp
                                        @if(isset($tariff_product_master_degree))
                                            @to_money($price) {{$item->tariff->currency->caption}}
                                        @else
                                            <a href="{{route("customer_group.buy.show_shopping_product",[$order,$customer,$item->product_id])}}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        @endif
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
            <div class="col-md-12" style="text-align: center" id="button_list">
                <a href="{{route("customer_group.buy.index_property",[$order,$goods_kind_property_value->product->goods_kind_id??0])}}"
                   class="btn btn-dark">بازگشت</a>

                <a href="{{route("customer_group.buy.shopping_cart",$order)}}" class="btn btn-success">
                    <i class="fa fa-shopping-cart"></i> مشاهده سبد خرید </a>

            </div>

        </div>


    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

