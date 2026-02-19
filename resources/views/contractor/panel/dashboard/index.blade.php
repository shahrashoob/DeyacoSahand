@extends('layouts.admin._master')
@section("page_header_title","داشبورد پیمانکاران   ")

@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("contractor.panel.dashboard._search_view",["route"=>"contractor.panel.dashboard.index"])

            <div class="card">
                <div class="card-header">
                    <h5>لیست دستورهای  پیمان</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>سریال دستور پیمان</th>
                                <th>شماره تخصیص</th>
                                <th>شماره سفارش</th>
                                <th> پیمانکار</th>
                                <th>نام محصول</th>
                                <th>تعداد</th>
                                <th>واحد</th>
                                <th>اولویت</th>
                                <th>وضعیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                @if($item->production)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a href="{{route("contractor.panel.dashboard.view",$item->id)}}">{{$item->production->serial(1)}}
                                            @if($item->production->production_type_id ==2)
                                                <i class="fa fa-vial text-dark"></i>
                                            @endif
                                        </a>
                                    </td>
                                    <td>{{$item->allocation->code()??"***"}}</td>
                                    <td>{{$item->production->order?$item->production->order->code():""}}</td>
                                    <td>{{$item->contractor->caption}}</td>
                                    <td>{{$item->product->code." - ".$item->product->caption}}</td>
                                    <td>{{$item->allocation_amount}}</td>
                                    <td>{{$item->product->unit->bach_caption??""}}</td>
                                    <td>{{$item->production->priority->caption??""}}</td>
                                    <td>{{$item->status->caption??""}}</td>
                                    <td>
                                        @if($item->getContractorPackingFormCount()> 0)
                                            <a href="{{route("contractor.panel.print.report_1",$item->id)}}"><i class="fa fa-download"></i> </a>

                                        @endif
                                    </td>


                                </tr>
                                @else
                                    <tr>
                                        <td colspan="10" class="alert-danger">
                                           کارت تولید {{$item->production_id}}
                                            حذف شده است.
                                        </td>
                                    </tr>
                                @endif

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
        function update_input_search(){
            request = $.ajax({
                url: "{{url("api/other/get_property_input_by_property_id")}}",
                type: "post",
                data: {
                    "id":$("#goods_kind_property_id").val(),
                    "value":$("#search").val()
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
