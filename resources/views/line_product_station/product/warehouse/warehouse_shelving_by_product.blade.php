@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"accounting.definition.cost_center.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>محل های قرار گیری
                        {{$product->fullCaption()}}
                        در
                        انبار ها

                    </h5>
                </div>
                <div class="card-block">

                    @include("line_product_station.product.warehouse._warehouse_shelving_list")
                </div>

            </div>
        </div>

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"accounting.definition.cost_center.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست بسته بندی های  کالا



                    </h5>
                </div>
                <div class="card-block">
                    @if(count($packing_forms))
                        <div class="table-responsive">
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>کد بسته بندی</th>
                                    <th>مقدار</th>
                                    <th>وزن ناخالص</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($packing_forms as $packing_form)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            {{$packing_form->code}}

                                        </td>
                                        <td>
                                            {{$packing_form->getAmount()}}

                                        </td>
                                        <td>
                                            {{$packing_form->gross_weight}}
                                        </td>


                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                        <div class="float-left">
                            نمايش رکوردهای
                            <b>{{$packing_forms->firstItem()}}</b>
                            تا
                            <b>{{$packing_forms->lastItem()}}</b>
                            از
                            <b>{{$packing_forms->total()}}</b>
                            رکورد موجود
                        </div>
                    @else
                        <div class="alert alert-warning">هیچ بسته بندی در محل های مجاز کالا قرار ندارد.</div>
                    @endif
                </div>
                <div class="text-center">
                    {{$packing_forms->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>

        @if(count($product_reservoirs))
            <div class="col-sm-12">
                {{--            @include("utility.public._search_view",["route"=>"accounting.definition.cost_center.index"])--}}
                <div class="card">
                    <div class="card-header">
                        <h5>لیست مخزن های کالا
                        </h5>
                    </div>
                    <div class="card-block">

                        <div class="table-responsive">
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>کد مخزن</th>
                                    <th>نام مخزن</th>
                                    <th>موجودی</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($product_reservoirs as $product_reservoir)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            {{$product_reservoir->reservoir->packing_form->code??""}}

                                        </td>
                                        <td>
                                            {{$product_reservoir->reservoir->caption}}

                                        </td>
                                        <td>
                                            {{$product_reservoir->reservoir->getAmount()}} {{$product_reservoir->reservoir->unit->caption}}

                                        </td>



                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                        <div class="float-left">
                            نمايش رکوردهای
                            <b>{{$product_reservoirs->firstItem()}}</b>
                            تا
                            <b>{{$product_reservoirs->lastItem()}}</b>
                            از
                            <b>{{$product_reservoirs->total()}}</b>
                            رکورد موجود
                        </div>
                    </div>
                    <div class="text-center">
                        {{$product_reservoirs->links('pagination::bootstrap-4')}}
                    </div>
                </div>
            </div>
        @endif




        <a href="{{route($dashboard_route)}}"
           class="btn btn-outline-dark">بازگشت</a>
    </div>

@endsection
@section("script")

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    @include("component.input.select2._script")
    <script>
        setTimeout(function() {
            $('input, select, textarea, button').prop('disabled', true);
        }, 1000);

    </script>
@endsection
