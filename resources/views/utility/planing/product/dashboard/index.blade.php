@extends('layouts.admin._master')
@section("page_header_title"," داشبورد برنامه ریزی - لیست کالاها ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("line_product_station.product._search_view",["route"=>"utility.planing.product.dashboard.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست همه کالا ها </h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد کالا
                                    <br/>
                                    رسته کالایی
                                </th>
                                <th> نام کالا
                                    <br/>
                                    روش برنامه ریزی کالا
                                </th>
                                <th>واحد</th>
                                <th>موجودی فعلی کالا</th>
                                <th>مقدار سفارش</th>
                                <th> مقدار باقی مانده سفارشات</th>
                                <th>بسته بندی در راه</th>
                                <th>مقدار کارت تولید و تخصیص</th>
                                <th>حداقل موجودی انبار</th>
                                <th>مقدار لازم جهت سفارشات جاری</th>
                                <th>مقدار لازم جهت تامین</th>
                                <th>مقدار دستور تامین</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        {{$item->code}}
                                        <br/>
                                        <span class="text-info">{{$item->goods_kind->caption??"---"}}</span>
                                    </td>
                                    <td>
                                        {{$item->caption}}
                                        <br/>
                                        <span class="text-info">{{$item->product_planing_algorithm->caption??"---"}}
                                        <br/>
                                            آخرین بررسی: {{$item->last_ran_planing_algorithm_datetime()}}
                                        </span>

                                    </td>

                                    <td>{{$item->unit->caption??""}}</td>
                                    <td>
                                        <a href="{{route("utility.planing.product.dashboard.packing_type_details",[$item])}}">
                                            {{$product_inventory[$item->id]==0?"":$product_inventory[$item->id]}}
                                        </a>

                                    </td>
                                    <td>{{$item->all_order_amount}}</td>
                                    <td>{{$item->remaining_order_amount??""}}</td>
                                    <td>{{isset($in_the_way_products[$item->id])?$in_the_way_products[$item->id]:""}}</td>
                                    <td>{{$production_sum[$item->id]==0?"":$production_sum[$item->id]}}</td>
                                    <td>{{$item->min_inventory}}</td>
                                    <th>{{$item->current_order_needed_amount??""}}</th>

                                    <th>
                                        {{--  PO  مقدار لازم جهت سفارشات جاری - مقدار کارت تولید و تخصیص--}}
                                       @php $po=max(0, ($item->current_order_needed_amount??0) - $production_sum[$item->id]);@endphp
                                        {{$po}}
                                    </th>
                                    <th>
{{--                                        {{max($po , )}}--}}
                                    </th>

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
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")
    @include("component.script_function.get_new_option")
    <script>
        var property_list =@php echo json_encode($property_list); @endphp;
        $("#goods_kind_id").change(function () {

            get_product_type()
        })

        function get_product_type() {
            request = $.ajax({
                url: "{{url("api/option/get")}}",
                type: "post",
                data: {
                    "type": $("#goods_kind_id").val(),
                    "id": $("#product_type_id").val(),
                }
            });
            request.done(function (response, textStatus, jqXHR) {

                $("#product_type_option").html(response);
            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // Log the error to the console
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });
        }


        function on_load_function() {
            if ($("#property_id").val() in property_list) {

                $("#property_value_id").parent().css("display", "")
                $("#property_value").parent().css("display", "none")
                get_new_option(
                    0,
                    $("#property_id").val(),
                    "محتوای مشخصه",
                    "property_value_id",
                    "goods_kind_property_option",
                    0
                )

            } else {

                $("#property_value_id").parent().css("display", "none")
                $("#property_value").parent().css("display", "")
            }
        }

        $("#goods_kind_id").change(function () {

            get_new_option(
                1,
                $("#goods_kind_id").val(),
                "مقدار مشخصه",
                "property_id",
                "get_property_by_goods_kind",
                0
            )

        })

        $("#property_id").change(function () {

            on_load_function();
        })

        on_load_function();

    </script>
@endsection
