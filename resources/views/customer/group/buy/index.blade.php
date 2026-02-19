@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")
    <div class="row">


        <div class="col-sm-12 ">

            <div class="card">
                <div class="card-header">
                    <h5>لیست رسته های کالایی </h5>
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

                                <th>رسته  کالایی</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($goods_kind_list as $item)
                                <tr>
                                    <td  class="center">{{$row++}}</td>

                                    <td>
                                        <a href="{{route("customer_group.buy.index_property",[$order,$item])}}">
                                            <h5 class="mb-1">{{$item->caption}}</h5>
                                        </a>

                                    </td>
                                    <td class="center">   </td>
                                    <td  class="center">

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

