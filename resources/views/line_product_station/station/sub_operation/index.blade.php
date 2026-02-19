@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>مدیریت
                        <b>
                           عملیات های فرعی
                        </b>
                        از عملیات
                        <b>
                            {{$station_operation->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد  عملیات فرعی</th>
                                <th>عنوان عملیات فرعی</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($station_operation->station_sub_operation as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>{{$item->getCode()}}</td>
                                    <td>
                                        <a href="{{ $item->station_operation->station->active_status_id == 1200?route("line_product_station.station.sub_operation.edit",[$station_operation,$item]):""}}">{{$item->caption}}</a>
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
            <div>
                <a href="{{route("line_product_station.station.operation.index",$station_operation->station_id)}}"
                   class="btn btn-outline-dark">بازگشت</a>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
