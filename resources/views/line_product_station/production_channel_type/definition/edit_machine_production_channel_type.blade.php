@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست ماشین های مجاز
                        <b>
                            {{ $machine_type->caption}}
                        </b>
                        برای کانال

                        <b>
                            {{$production_channel_type->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("line_product_station.production_channel_type.definition.update_machine_production_channel_type",[$machine_type,$production_channel_type])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>

                                    </th>
                                    <th>نام ماشین</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($machine_type->machines as $item)
                                    <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                        <td>{{++$row}}</td>
                                        <td>
                                            <input type="checkbox"
                                                   name="machine[{{$item->id}}]"
                                                    {{isset($machine_ids[$item->id]) ?"checked":""}}
                                            >
                                        </td>
                                        <td>
                                            {{$item->caption}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                        <a class="btn btn-outline-dark"
                           href="{{route("line_product_station.production_channel_type.definition.edit",$production_channel_type)}}">بازگشت</a>
                        <button class="btn btn-primary" type="submit">ذخیره تغییرات</button>

                    </form>
                </div>
                <div>
                    {{-- <a href="{{route("line_product_station.machine_type.index",$machine_type->station_id)}}" class="btn btn-outline-dark">بازگشت</a> --}}

                </div>
            </div>

        </div>

        @endsection
        @section("styles")
            <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
            <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
