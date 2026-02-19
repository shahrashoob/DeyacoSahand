@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>مدیریت
                        <b>
                           عملیات ها
                        </b>
                        از ایستگاه کاری
                        <b>
                            {{$station->caption}}
                        </b>
                        <a class="btn btn-success"
                           href="{{route("line_product_station.station.operation.create",$station)}}"><i
                                class="fa fa-plus-circle"></i> افزودن عملیات جدید </a>
                    </h5>
                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد عملیات</th>
                                <th>عنوان عملیات</th>
                                <th>نوع عملیات</th>
                                <th>دسته عملیات</th>
                                <th>روش حرکت مواد در ماشین</th>
                                <th>تعداد عملیات های فرعی</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($station->operations as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>{{$item->getCode()}}</td>
                                    <td>
                                        <a href="{{ $item->station->active_status_id == 1200?route("line_product_station.station.operation.edit",[$station,$item]):""}}">{{$item->caption}}</a>
                                    </td>
                                    <td>{{$item->station_operation_type->caption??""}}</td>
                                    <td>{{$item->station_operation_category->caption??""}}</td>
                                    <td>{{$item->discharge_type->caption??""}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.station.sub_operation.index",$item)}}">
                                            {{$item->station_sub_operation()->count()}}

                                            عملیات فرعی
                                        </a>
                                        <a class="text-success" href="{{route("line_product_station.station.sub_operation.create",$item)}}"><i class="fa fa-plus-circle"></i>  </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
            <div>
                <a href="{{route("line_product_station.station.index",$station->line_id)}}"
                   class="btn btn-outline-dark">بازگشت</a>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
