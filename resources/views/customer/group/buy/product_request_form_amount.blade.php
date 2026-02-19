@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")

    <div class="row">
        <div class="col-xl-12">
            <h3>{{$product->caption}}</h3>
            <hr/>
        </div>

        <div class="col-xl-12">
            <div class="card">

                <div class="card-block user-chart">

                    <div class="table-responsive">
                        <table class="table table-hover center">
                            <thead>
                            <tr>
                                <th class="center">ردیف</th>

                                <th>شماره درخواست</th>
                                <th>مشتری</th>
                                <th>مقدار باقی مانده مجوز</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td class="center">{{$row++}}</td>

                                    <td>
                                        {{$item->code}}

                                    </td>
                                    <td>
                                        {{$item->order->customer->caption}}

                                    </td>

                                    <td>
                                        {{$item->amount_remaining}}
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <a href="{{route("customer_group.buy.show_shopping_product",[$order,$order->customer_id,$product])}}" class="btn btn-outline-dark">
                        بازگشت </a>
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
    </script>
@endsection

@section("styles")
    <link href="{{asset("assets/plugins/select2/css/select2.min.css")}}" rel="stylesheet">

    <style>


    </style>
@endsection
