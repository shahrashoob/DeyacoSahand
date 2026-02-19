@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت پیمانکاران")

@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("contractor.admin.dashboard._search_view",["route"=>"contractor.admin.dashboard.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست دستور های پیمان</h5>
                    <a href="{{route("contractor.admin.contractor_allocation_quick.index")}}" class="btn btn-primary"> تخصیص سریع</a>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>سریال دستور پیمان</th>
                                <th>شماره سفارش</th>
                                <th>نام محصول</th>
                                <th>تعداد</th>
                                <th>واحد</th>
                                <th>اولویت</th>
                                <th>وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a href="{{route("contractor.admin.dashboard.view_card",$item->production_card_id)}}">
                                            {{$item->serial(1)}}
                                            @if($item->production_type_id ==2)
                                                <i class="fa fa-vial text-dark"></i>
                                            @endif
                                        </a>
                                    </td>
                                    <td>
                                        {{$item->order?$item->order->code():""}}
                                    </td>
                                    <td>{{$item->product->code." - ".$item->product->caption}}</td>
                                    <td>{{$item->number}}</td>
                                    <td>{{$item->product->unit->bach_caption}}</td>
                                    <td>{{$item->priority->caption??""}}</td>
                                    <td>{{$item->getStatus()}}</td>


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

