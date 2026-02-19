@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت تامین کنندگان")

@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("utility.public._search_view",["route"=>"supplier.admin.dashboard.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست درخواست های تامین</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>تاریخ ایجاد</th>
                                <th>نام تامین کننده</th>
                                <th>شماره تخصیص</th>
                                <th>نام محصول</th>
                                <th>مقدار</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>

                                    <td>{{$item->allocation->get_create_date()}}</td>
                                    <td>{{$item->allocation->supplier->caption}}</td>
                                    <td>

                                        <a href="{{route("supplier.admin.dashboard.view",$item->id)}}">{{$item->allocation_id}}</a>
                                    </td>
                                    <td>{{$item->product->code." - ".$item->product->caption}}</td>
                                    <td>{{$item->allocation_amount}} {{$item->product->unit->bach_caption}}</td>

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
    <script>
        $("#goods_kind_property_id").change(function () {
            update_input_search();
        })

        function update_input_search() {
            request = $.ajax({
                url: "{{url("api/other/get_property_input_by_property_id")}}",
                type: "post",
                data: {
                    "id": $("#goods_kind_property_id").val(),
                    "value": $("#search").val()
                }
            });
            request.done(function (response, textStatus, jqXHR) {

                $("#search_input").html(response);
            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // Log the error to the console
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });
        }

        update_input_search();

    </script>
@endsection

