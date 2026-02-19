@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")
    <div class="row">


        <div class="col-sm-12 ">

            <div class="card">
                <div class="card-header">
                    <h5>لیست کالا ها
                        @if(isset($goods_kind_display_property->goods_kind_property->caption))
                            بر اساس
                            {{$goods_kind_display_property->goods_kind_property->caption}}
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
                        <table class="table table-hover center">
                            <thead>
                            <tr>
                                <th  class="center">ردیف</th>

                                <th>
                                    @if(isset($goods_kind_display_property->goods_kind_property->caption))

                                        {{$goods_kind_display_property->goods_kind_property->caption}}
                                    @else
                                        نام کالا
                                    @endif

                                </th>
                            </tr>

                            </thead>
                            <tbody>
                            <tr>
                                <td></td>
                                <td>
                                    <form id="form1" style="display: inline" action="" method="post" novalidate="novalidate">
                                        @csrf

                                        <input type="text" name="search"  value="{{$search}}" style="height: 30px; ">
                                    <button type="submit" class="btn btn-primary btn-sm">جستجو</button>
                                    </form>
                                </td>
                            </tr>
                            @php $row=1;@endphp
                            @foreach($property_list as $item)
                                <tr>
                                    <td  class="center">{{$row++}}</td>

                                    <td>
                                        <a href="{{route("customer_group.buy.index_product",[$order,$item["id"]])}}">
                                            <h5 class="mb-1">
                                                {{isset($property_list_values_option[$item["value"]])?$property_list_values_option[$item["value"]]:$item["value"]}}
                                            </h5>
                                        </a>

                                    </td>


                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>

            </div>
            <div class="col-md-12" style="text-align: center" id="button_list">
                <a href="{{route("customer_group.order.index")}}" class="btn btn-dark">بازگشت</a>

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

