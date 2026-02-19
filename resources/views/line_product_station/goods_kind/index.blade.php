@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>رسته های کالا
                        <a class="btn btn-success" href="{{route("line_product_station.goods_kind.create")}}"> <i
                                class="fa fa-plus"></i> افزودن رسته کالایی </a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center" >
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> کد</th>
                                <th> عنوان رسته</th>
                                <th> طبقه بندی ها</th>
                                <th> مشخصه ها</th>
                                <th colspan="2">وضعیت ها</th>
                                <th>درجه بندی</th>
                                <th>انواع بسته بندی</th>
                                <th>نقص های رسته کالایی</th>
                                <th> روش برنامه ریزی تولید</th>
                                <th colspan="2">تنظیمات </th>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <th>  کارت تولید</th>
                                <th>  فرم تولید</th>
                                <td colspan="4"></td>
                                <th>تعریف کالا</th>
                                <th> طراحی کالا </th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>{{$item->getCode()}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.goods_kind.edit",$item)}}">{{$item->caption}}</a>
                                    </td>
                                    @if($item->active_status_id == 1210)
                                        <td colspan="10"></td>
                                    @else


                                    <td >
                                        <a href="{{route("line_product_station.goods_kind.classification.index",$item)}}"> {{$item->classification->count()}}
                                            طبقه بندی </a>
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.goods_kind.property.index",$item)}}"> {{$item->property->count()}}
                                            مشخصه </a>
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.goods_kind.production_waiting_status",$item)}}"> {{$item->production_waiting_status->count()}}
                                            وضعیت </a>
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.goods_kind.production_form_status",$item)}}"> {{$item->production_form_status->count()}}
                                            وضعیت </a>
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.degree.index",$item)}}"> {{$item->degree->count()}}
                                             نوع درجه </a>
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.goods_kind.packing_type_list",$item)}}"> {{$item->packing_type->count()}}
                                             نوع بسته بندی </a>
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.goods_kind.product_fault.index",$item)}}"> {{$item->product_fault->count()}}
                                             نقص </a>
                                    </td>
                                    <td>
                                        {{$item->production_algorithm_type->caption??""}}
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.goods_kind.setting.index",$item)}}">
                                            <i class="fa fa-cog"></i> </a>
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.goods_kind.setting.product_creation_priority.index",$item)}}">
                                            <i class="fa fa-cog"></i> </a>
                                    </td>
                                    @endif
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
