@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست ایستگاه های کاری خط {{$line->caption}}
                        <a class="btn btn-success" href="{{route("line_product_station.station.create",$line)}}"> <i
                                class="fa fa-plus"></i> افزودن ایستگاه کاری </a>

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد ایستگاه</th>
                                <th> عنوان ایستگاه</th>
                                <th>تعداد عملیات</th>
                                <th>تعداد دسته عملیات</th>
                                <th>نام و کد خط</th>
                                <th> گروه های ماشین </th>
                                <th colspan="2" style="border-bottom: 2px solid"> ویژگی ها </th>
                                <th>انبارک ایستگاه</th>
                                <th>وضعیت ایستگاه</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>  ماشین </th>
                                <th> ماشین -محصول </th>
                                <th></th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}123</td>
                                    <td>
                                        {{$item->getCode()}}
                                    </td>
                                    <td>
                                        <a href="{{$item->line->active_status_id == 1200?route("line_product_station.station.edit",$item):""}}">{{$item->caption}}</a>
                                    </td>

                                    <td>
                                        <a href="{{route("line_product_station.station.operation.index",$item)}}">
                                            {{$item->operations()->count()}} عملیات
                                        </a>
                                        @if($item->active_status_id == 1200)
                                            <a class="text-success"
                                               href="{{route("line_product_station.station.operation.create",$item)}}"><i
                                                    class="fa fa-plus-circle"></i>  </a>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.station.station_category.index",$item)}}">
                                            {{$item->station_categories()->count()}} دسته عملیات
                                        </a>
                                    </td>

                                    <td>{{($item->line->code??"").(" - ".$item->line->caption??"")}}</td>
                                    <td>

                                        <a href="{{route("line_product_station.machine_type.index",$item)}}">
                                            {{$item->machine_type->where("active_status_id",1200)->count()}}
                                            گروه فعال </a>
                                        @if($item->active_status_id == 1200)
                                            <a class="text-success"
                                               href="{{route("line_product_station.machine_type.create",$item)}}"><i
                                                    class="fa fa-plus-circle"></i>  </a>
                                        @endif

                                    </td>


                                    <td>
                                        <a href="{{$item->line->active_status_id == 1200?route("line_product_station.machine_property.index",$item):""}}">
                                            {{$item->machine_property->count()}} ویژگی
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{$item->line->active_status_id == 1200?route("line_product_station.machine_product_property.index",$item):""}}">
                                            {{$item->machine_product_property->count()}} ویژگی
                                        </a>
                                    </td>
                                    <td>

                                        <a href="{{route("line_product_station.station.warehouse_index",$item)}}">
                                            {{$item->warehouses->count()}}
                                            انبارک</a>
                                        @if($item->active_status_id == 1200)
                                            <a class="text-success"
                                               href="{{route("line_product_station.station.warehouse_create",$item)}}"><i
                                                    class="fa fa-plus-circle"></i>  </a>
                                        @endif
                                    </td>
                                    <td>
                                        {{($item->active_status->caption??"")}}

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
            <div>
                <a href="{{route("line_product_station.line.index")}}"
                   class="btn btn-outline-dark">بازگشت</a>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
