@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between"><h5 class="mb-0">
                            لیست محصولات

                            {{$customer->parent->caption}}
                        </h5>
                        <div>
                            <a  class="btn btn-outline-success " href="{{route("customer_group.retail_customer.buy.shopping_cart")}}">
                                <i class="fa fa-shopping-cart"></i> مشاهده سبد خرید
                            </a>
                            <a  class="btn btn-outline-primary " href="{{route("customer_group.retail_customer.order_list.index")}}">
                                <i class="fa fa-shopping-cart"></i> سفارش های من
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        @foreach($list as $item)
                            <div class="col-sm-6 col-lg-4 col-xxl-3">
                                <div class="card border">
                                    <div class="card-body p-2">
                                        <div class="position-relative center">
                                            <img class="rounded-circle" style="width: 150px;height: 150px;"
                                                 src="{{asset("upload/product/".$item->product->image->filename??'')}}"
                                                 onerror="this.onerror=null;this.src='{{url("upload/product/product.png")}}';"
                                                 alt="img" class="img-fluid w-100">


                                            <div class="position-absolute top-0 end-0 p-2"><span
                                                        class="badge text-bg-light text-uppercase">Free</span></div>
                                        </div>
                                        <ul class="list-group list-group-flush my-2">
                                            <li class="list-group-item px-0 py-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 me-2"><h6 class="mb-1">
                                                            {{$item->getCaption()}}
                                                        </h6>

                                                    </div>

                                                </div>
                                            </li>
                                            <li class="list-group-item px-0 py-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 me-2"><p class="mb-0">کد کالا: </p></div>
                                                    <div class="flex-shrink-0"><p
                                                                class="text-muted mb-0">{{$item->product->code}}</p>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item px-0 py-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 me-2"><p class="mb-0">قیمت</p></div>
                                                    <div class="flex-shrink-0"><p class="text-muted mb-0">
                                                            @php
                                                                $tariff_product_master_degree=$item->product->getProductTariffWithMasterDegree($item->tariff_id);
                                                                $price=$tariff_product_master_degree->consumer_price;

                                                            @endphp
                                                            @if(isset($tariff_product_master_degree))
                                                                @to_money($price) {{$item->tariff->currency->caption}}
                                                            @else
                                                                <i class="fa fa-eye"></i>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </li>

                                        </ul>
                                      <div class="center">
                                          <a class="btn btn-sm btn-outline-primary mb-2" href="{{route("customer_group.retail_customer.buy.add_to_shopping_cart",$item->id)}}">افزودن به سبد خرید</a>
                                      </div>
                                    </div>
                                </div>
                            </div>

                        @endforeach

                    </div>
                </div>
            </div>
        </div>


    </div>


    <div class="col-md-12" style="text-align: center" id="button_list">


        <div class="float-left">
            نمايش رکوردهای
            <b>{{$list->firstItem()}}</b>
            تا
            <b>{{$list->lastItem()}}</b>
            از
            <b>{{$list->total()}}</b>
            رکورد موجود


        </div>
        <br/>
        <div class="text-center">
            {{$list->links('pagination::bootstrap-4')}}
        </div>
        <a  class="btn btn-success " href="{{route("customer_group.retail_customer.buy.shopping_cart")}}">
            <i class="fa fa-shopping-cart"></i> مشاهده سبد خرید
        </a>

    </div>




    {{--    <div class="col-sm-12 ">--}}

    {{--        <div class="card">--}}
    {{--            <div class="card-header">--}}
    {{--                <h5> لیست محصولات فروشگاه--}}

    {{--                    {{$customer->parent->caption}}--}}
    {{--                </h5>--}}
    {{--                <div class="card-header-right">--}}


    {{--                </div>--}}

    {{--            </div>--}}
    {{--            <div class="card-block pb-0">--}}

    {{--                <div class="table-responsive">--}}
    {{--                    <table class="table table-hover">--}}
    {{--                        <thead>--}}
    {{--                        <tr>--}}
    {{--                            <th class="center">ردیف</th>--}}

    {{--                            <th></th>--}}

    {{--                            <th>مشخصات کالا (خدمت)</th>--}}
    {{--                            <th class="center">واحد</th>--}}
    {{--                            <th class="center">--}}
    {{--                                مبلغ واحد--}}
    {{--                                --}}{{--                                    @if($customer->price_displayed_to_customer_with_tax)--}}
    {{--                                --}}{{--                                            (با احتصاب ارزش افزوده)--}}
    {{--                                --}}{{--                                    @else--}}
    {{--                                --}}{{--                                         (بدون احتصاب ارزش افزوده)--}}
    {{--                                --}}{{--                                    @endif--}}

    {{--                            </th>--}}
    {{--                        </tr>--}}

    {{--                        </thead>--}}
    {{--                        <tbody>--}}
    {{--                        @php $row=$list->firstItem();@endphp--}}
    {{--                        @foreach($list as $item)--}}
    {{--                            <tr>--}}
    {{--                                <td class="center">{{$row++}}</td>--}}

    {{--                                <td>--}}

    {{--                                    <img class="rounded-circle" style="width: 150px;height: 150px;"--}}
    {{--                                         src="{{asset("product_image/".$item->product->getImgName())}}"--}}
    {{--                                         onerror="this.onerror=null;this.src='{{url("product_image/product.png")}}';"--}}
    {{--                                         alt="activity-user">--}}

    {{--                                </td>--}}

    {{--                                <td>--}}

    {{--                                    <h5 class="mb-1">--}}

    {{--                                        {{$item->getCaption()}}--}}
    {{--                                    </h5>--}}
    {{--                                    <p class="m-0">کد کالا: {{$item->product->code}}--}}
    {{--                                        @if($item->service_id)--}}
    {{--                                            &nbsp;--}}
    {{--                                            &nbsp;--}}
    {{--                                            کد خدمت: {{$item->service->code??""}} </p>--}}
    {{--                                    @endif--}}
    {{--                                    @include("customer.group._product_offer",["product"=>$item->product])--}}


    {{--                                </td>--}}
    {{--                                <td class="center">{{$item->product->unit->bach_caption}}   </td>--}}
    {{--                                <td class="center">--}}
    {{--                                    @php--}}
    {{--                                        $tariff_product_master_degree=$item->product->getProductTariffWithMasterDegree($item->tariff_id);--}}
    {{--                                        $price=$tariff_product_master_degree->customer_price;--}}

    {{--                                    @endphp--}}
    {{--                                    @if(isset($tariff_product_master_degree))--}}
    {{--                                        @to_money($price) {{$item->tariff->currency->caption}}--}}
    {{--                                    @else--}}
    {{--                                        <a href="{{route("customer_group.buy.show_shopping_product",[$order,$customer,$item->product_id])}}">--}}
    {{--                                            <i class="fa fa-eye"></i>--}}
    {{--                                        </a>--}}
    {{--                                    @endif--}}
    {{--                                </td>--}}

    {{--                            </tr>--}}
    {{--                        @endforeach--}}
    {{--                        </tbody>--}}

    {{--                    </table>--}}
    {{--                </div>--}}
    {{--                <div class="float-left">--}}
    {{--                    نمايش رکوردهای--}}
    {{--                    <b>{{$list->firstItem()}}</b>--}}
    {{--                    تا--}}
    {{--                    <b>{{$list->lastItem()}}</b>--}}
    {{--                    از--}}
    {{--                    <b>{{$list->total()}}</b>--}}
    {{--                    رکورد موجود--}}


    {{--                </div>--}}
    {{--            </div>--}}
    {{--            <div class="text-center">--}}
    {{--                {{$list->links('pagination::bootstrap-4')}}--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--        <div class="col-md-12" style="text-align: center" id="button_list">--}}


    {{--            <a href="#" class="btn btn-success">--}}
    {{--                <i class="fa fa-shopping-cart"></i> مشاهده سبد خرید </a>--}}

    {{--        </div>--}}

    {{--    </div>--}}

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

