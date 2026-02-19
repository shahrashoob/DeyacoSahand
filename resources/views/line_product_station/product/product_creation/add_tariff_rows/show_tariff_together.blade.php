@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <form id="form1"
          action="{{route($route_path."submit_tariff_together",[$product,$productTariffPricing,$product_creation_process])}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header">
                        <h5>تعرفه گذاری همراه با کالای {{$product->caption}}</h5>
                    </div>
                    <div class="card-block">
                        <div class="row">
                            <div class="col-md-12">
                                <div style="overflow: auto">
                                    <table class="table col-md-6 center">
                                        <tr>
                                            <th>ردیف</th>
                                            <th>کد کالا</th>
                                            <th> نام کالا</th>
                                            <th>درجه</th>
                                            <th>نوع بسته بندی</th>
                                            <th>انبار</th>
                                            <th>نوع فروش</th>
                                            <th>نام و کد خدمت</th>
                                            <th>حداقل خرید ({{$product->unit->caption}})</th>
                                            <th>حداکثر خرید ({{$product->unit->caption}})</th>

                                            <th>درصد عوارض</th>
                                            <th>درصد مالیات بر <br/>ارزش افزوده</th>
                                            <th></th>

                                        </tr>
                                        @php $row=0; @endphp
                                        @foreach($products as $item)
                                            <tr class="alert-warning">
                                                <td>{{++$row}}</td>
                                                <td>
                                                    {{$item->code}}
                                                </td>
                                                <td>
                                                    {{$item->caption}}
                                                </td>
                                                <td>
                                                    {{$productTariffPricing->degree->caption}}
                                                </td>
                                                <td>
                                                    <a href="#" title="{{$productTariffPricing->packing_type->caption}}">
                                                        {{$productTariffPricing->packing_type->code}}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{$productTariffPricing->warehouse->caption}}
                                                </td>
                                                <td>
                                                    {{$productTariffPricing->type_of_sale_of_product->caption}}
                                                </td>
                                                <td>
                                                    {{$productTariffPricing->service_id?$productTariffPricing->service->fullCaption():""}}
                                                </td>

                                                <td>
                                                    {{number_format($productTariffPricing->min_buy)}}
                                                </td>
                                                <td>
                                                    {{number_format($productTariffPricing->max_buy)}}
                                                </td>

                                                <td>
                                                    {{$productTariffPricing->tax}}
                                                </td>
                                                <td>
                                                    {{$productTariffPricing->fare}}
                                                </td>
                                                <th>

                                                </th>

                                            </tr>
                                        @endforeach


                                    </table>
                                </div>

                            </div>
                            <div class="col-md-12">
                                @include("component.input._hidden",["id"=>"product_ids","value"=>$products_json])

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header">
                        <h5>  تعرفه ها</h5>
                    </div>
                    <div class="card-block">
                        <div class="row">
                            <div class="col-md-12">
                                <div style="overflow: auto">
                                    <table class="table col-md-6 center">
                                        <tr>
                                            <th></th>
                                            <th>ردیف</th>
                                            <th>تعرفه</th>


                                        </tr>
                                        @php $row=0; @endphp
                                        @foreach($other_product_tariff_pricing as $item_product_pricing)
                                            <tr class="alert-warning">
                                                <td><input type="checkbox" checked name="other_product_tariff_pricing[{{$item_product_pricing->id}}]"></td>
                                                <td>{{++$row}}</td>
                                                <td>
                                                    {{$item_product_pricing->tariff->caption}}
                                                </td>

                                                <th>

                                                </th>

                                            </tr>
                                        @endforeach


                                    </table>
                                </div>

                            </div>
                            <div class="col-md-12">
                                @include("component.input._hidden",["id"=>"product_ids","value"=>$products_json])
                                <a href="{{route($route_path."add_tariff_together",[$product,$productTariffPricing,$product_creation_process])}}" class="btn btn-outline-dark"> بازگشت</a>
                                <button type="submit" class="btn btn-primary">تایید و افزودن به تعرفه</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </form>

@endsection
@section("styles")


@endsection

@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "product_ids": "required",
            }
        });

    </script>
@endsection

