@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("line_product_station.product._search_view",["route"=>"line_product_station.product.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست همه کالا ها </h5>
                    @if (  $post_user->checkButtonPermission("line_product_station.product.create" ) )
                        <a class="btn btn-success" href="{{route("line_product_station.product.create")}}"> <i
                                    class="fa fa-plus"></i> افزودن کالای جدید </a>
                    @endif

                    @if (  $post_user->checkButtonPermission("line_product_station.product.product_creation.new_form.index" ) )
                        <a class="btn btn-success"
                           href="{{route("line_product_station.product.product_creation.dashboard.index")}}"> <i
                                    class="fa fa-star"></i> لیست درخواست های طراحی کالا </a>
                    @endif
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد</th>
                                <th> عنوان</th>
                                <th>ورژن</th>
                                <th>واحد</th>
                                <th>نوع تامین</th>
                                <th>گروه خط تولید</th>
                                <th>وضعیت</th>
                                <th>محل نگهداری کالا</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.product.edit",$item)}}">{{$item->code}}</a>
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.product.edit",$item)}}">{{$item->caption}}

                                        </a>
                                    </td>
                                    <td>
                                        @if($item->version)
                                            V{{$item->version->version_code}}
                                        @endif
                                    </td>
                                    <td>{{$item->unit->caption??""}}</td>
                                    <td>{{$item->supply_type->caption??""}}

                                    </td>
                                    <td>{{$item->line_group->caption??""}}</td>
                                    <td>{{$item->active_status->caption??""}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.product.warehouse.warehouse_shelving",$item)}}"><i
                                                    class="fa fa-th-large"></i> </a>
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
