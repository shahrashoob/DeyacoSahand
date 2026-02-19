@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")

    <div class="row">
        <div class="col-xl-12">
            <h3>{{$tariff_product_list[0]->getCaption()}}</h3>
            <hr/>
        </div>

        <div class="col-xl-12">
            <div class="card">

                <div class="card-block user-chart">

                    @if($tariff_product_list[0]->service_id)
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>نام خدمت:</label>
                                {{$tariff_product_list[0]->service->code." - ".$tariff_product_list[0]->service->caption}}
                            </div>
                        </div>

                    @endif

                    @include("component.input._lable_product",["id"=>"product1","lable"=>" نام کالا",
                             "product_property"=>$product,
                             "value"=>$product->code." -". $product->caption,"class_col"=>"col-md-12"])

                    @if($setting["show_product_image"]->integer_value==1)
                        <div class="col-md-12 center">
                            <img class="" style="max-height:400px "
                                 src="{{asset("upload/product/".($product->image->filename??''))}}"
                                 onerror="this.onerror=null;this.src='{{url("product_image/product.png")}}';"
                                 alt="activity-user">
                        </div>
                    @endif

                </div>
            </div>
        </div>


        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5>خرید کالا (خدمت) بر اساس درجه بندی</h5>
                    <div class="card-header-right">

                        <a href="{{route("customer_group.buy.shopping_cart",$order)}}">
                            <i class="fa fa-shopping-cart fa-2x  label label-success"> <span
                                        class="label text-white f-24"
                                        style="font-family: IRANSans">{{$order->orderList->count()}}</span></i>
                        </a>

                    </div>
                </div>
                <div class="card-block invoice-summary">
                    <form id="form1" style="display: inline"
                          action="{{route("customer_group.buy.add_to_shopping_cart",[$order,$customer,$product])}}"
                          method="post"
                          autocomplete="off">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-hover" style="text-align: center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>درجه</th>
                                    <th>بسته بندی</th>
                                    <th>حداقل خرید</th>

                                    @if($post_user->checkButtonPermission("sales.warehouse_inventory_column"))
                                        <th>موجودی انبار</th>
                                        <th>مقدار مجوزها</th>
                                    @endif

                                    @if($post_user->checkButtonPermission("sales.reserve_amount"))
                                        <th>مقدار سفارشات</th>
                                    @endif

                                    @if($post_user->checkButtonPermission("sales.amount_to_order"))
                                        <th>مقدار قابل سفارش</th>
                                    @endif

                                    <th>مبلغ واحد
                                        {{--                                        @if($customer->price_displayed_to_customer_with_tax)--}}
                                        {{--                                            (با احتصاب ارزش افزوده)--}}
                                        {{--                                        @else--}}
                                        {{--                                             (بدون احتصاب ارزش افزوده)--}}
                                        {{--                                        @endif--}}

                                    </th>
                                    <th> مقدار {{$product->unit->caption}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $row=1;@endphp
                                @foreach($tariff_product_list as $item)
                                    @php
                                        $inventory=$item->product->getInventory($item->degree->id);
                                        $amount=$item->product->getReserveAmount($item->degree->id);
                                    @endphp
                                    @if(
	                                    $setting["effective_inventory_in_order"]->integer_value==0 ||
	                                    max($inventory-$amount,0)>0 ||
	                                    $item->degree->degree_type_id == 1
	                                    )
                                        <tr>
                                            <td>{{$row++}}</td>
                                            <td>{{$item->degree->caption}} </td>
                                            <td style="width: 30%">

                                                @php $packing_items=$item->getOption("packing_type_".$item->id)["items"];@endphp
                                                @if(count($packing_items)>1)

                                                    <a class="" data-toggle="collapse" href="#collapseExample"
                                                       role="button" aria-expanded="true"
                                                       aria-controls="collapseExample">همه بسته بندی های مجاز</a>

                                                    <div class="collapse " id="collapseExample" style="">

                                                        @include("component.input._select_simple",["id"=>"data[packing_type][".$item->id."][]","class"=>"js-example-rtl col-sm-12","multiple"=>"multiple","option"=>$packing_items])

                                                    </div>

                                                @else
                                                    {{$packing_items[0]["text"]}}
                                                    @include("component.input._hidden",["id"=>"data[packing_type][".$item->id."][]","value"=>$packing_items[0]["value"]])
                                                @endif

                                            </td>
                                            <td>{{$item->min_buy}}</td>

                                            @if($post_user->checkButtonPermission("sales.warehouse_inventory_column"))
                                                <td>
                                                    <button class="text-primary" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false"
                                                            style="background: none;border: none;">

                                                        {{ $inventory  }}

                                                    </button>
                                                    <div class="dropdown-menu center" x-placement="bottom-start"
                                                         style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(163px, 210px, 0px);">
                                                        @foreach($packing_type_caption as $item_packing_type)
                                                            <span class="dropdown-item"
                                                                  href="#">{{$item_packing_type["caption"]}}
                                                            :
                                                                {{$item_packing_type["inventory"]}} {{$product->unit->caption}}
                                                            </span>

                                                        @endforeach

                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{route("customer_group.buy.product_request_form_amount",[$item->product_id,$order])}}">

                                                    {{$product_request_form_remaining}}
                                                    </a>
                                                </td>
                                            @endif

                                            @if($post_user->checkButtonPermission("sales.reserve_amount"))
                                                <td>
                                                    <a href="{{route("customer_group.buy.reserve_amount",[$item->product_id,$order])}}">
                                                        {{ $amount }}
                                                    </a>

                                                </td>
                                            @endif

                                            @if($post_user->checkButtonPermission("sales.amount_to_order"))
                                                <td>{{$item->degree->degree_type_id==1?$item->max_buy:min(  max($inventory-$amount,0),$item->max_buy)  }}</td>
                                            @endif

                                            <td>
                                                @php
                                                    $price=$item->fea;
                                                        if($customer->price_displayed_to_customer_with_tax){
                                                        $price=$price*(1+($item->tax + $item->fare)/100);
                                                    }
                                                @endphp
                                                @to_money($price)

                                                {{$item->tariff->currency->caption??""}}
                                            </td>
                                            <td>
                                                @include("component.input._number_sample",[
                                                        "id"=>'data['.$item->id."]",
                                                         "max"=>$item->max_buy,
                                                         "min"=>$item->min_buy,
                                                         "required"=>1,
                                                         "value"=>(isset($orderListPluck[$item->degree_id."_".$item->product_id."_".$packing_items[0]["value"]])?$orderListPluck[$item->degree_id."_".$item->product_id."_".$packing_items[0]["value"]]:"")
                                                         ])
                                            </td>
                                        </tr>
                                    @endif

                                @endforeach
                                @if($row==1)
                                    <tr>
                                        <td colspan="8">امکان سفارش برای این کالا وجود ندارد</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12" style="text-align: center" id="button_list">
                            <a href="{{route("customer_group.buy.index_product",[$order,$goods_kind_property_value])}}"
                               class="btn btn-dark">بازگشت</a>

                            @if($row>1)
                                <button id="btn_confirm" class="btn btn-primary" type="submit">
                                    <i class="fa fa-plus"></i>
                                    افزودن به سبد خرید
                                </button>
                            @endif
                            <a href="{{route("customer_group.buy.shopping_cart",$order)}}" class="btn btn-success">
                                <i class="fa fa-shopping-cart"></i> مشاهده سبد خرید </a>
                            @include("component.button.loading")
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection

@section("scripts")
    @include("component.button._loading_script",["btn_id"=>"btn_confirm"])
    <script src="{{asset("assets/plugins/select2/js/select2.full.min.js")}}"></script>
    <script>
        $(".js-example-rtl").select2({
            dir: "rtl"
        });
        $('#form1').validate({
            rules: {
                delivery_datetime_value: "required",
            }
        });
    </script>
@endsection

@section("styles")
    <link href="{{asset("assets/plugins/select2/css/select2.min.css")}}" rel="stylesheet">

    <style>


    </style>
@endsection
