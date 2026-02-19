@extends('layouts.admin._master')

@section("page_header_title","داشبورد انبار ")
@section("content")

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>چاپ / انبارگردانی شماره {{$warehouse_handling->id}}
                        - {{$warehouse_handling->warehouse->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="row">

                        @include("warehouse.warehouse_handling.dashboard._info")

                        <div class="col-md-12">

                            <div class="table-responsive">
                                @include("warehouse.warehouse_handling.print._table",["first_row"=>$product_list->firstItem()])
                            </div>
                            <div class="float-left">
                                نمايش رکوردهای
                                <b>{{$product_list->firstItem()}}</b>
                                تا
                                <b>{{$product_list->lastItem()}}</b>
                                از
                                <b>{{$product_list->total()}}</b>
                                رکورد موجود


                            </div>
                        </div>
                        <div class="text-center">
                            {{$product_list->links('pagination::bootstrap-4')}}
                        </div>

                        <div class="col-md-12">
                            <br/>
                            <a href="{{route("wh.warehouse_handling.dashboard.view",$warehouse_handling)}}"    class="btn btn-outline-dark">بازگشت</a>
                            <a href="{{route("wh.warehouse_handling.print.print",[$warehouse_handling,$product_list->firstItem()])}}"    class="btn btn-info">چاپ دستور انبارگردانی</a>
                            <a href="{{route("wh.warehouse_handling.print.download",[$warehouse_handling,$product_list->firstItem()])}}"    class="btn btn-info">دانلود دستور انبارگردانی </a>

                        </div>
                    </div>
                </div>
            </div>


        </div>

@endsection