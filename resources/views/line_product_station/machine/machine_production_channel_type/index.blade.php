@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست کانال های تولید
                        <b>
                            {{ $machine->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("line_product_station.machine.production_channel_type.store",[$machine->id])}}"
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
                                    <th>نوع کانال تولید</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($list_machine_type as $item)
                                    <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                        <td>{{++$row}}</td>
                                        <td>
                                            <input type="checkbox"
                                                   name="production_channel_type[{{$item->production_channel_type_id}}]"
                                                    {{isset($production_channel_type_ids[$item->production_channel_type_id]) ?"checked":""}}
                                            >
                                        </td>
                                        <td>
                                            {{$item->production_channel_type->caption}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                        <a class="btn btn-outline-dark"
                           href="{{route("line_product_station.machine.index",$machine->machine_type_id)}}">بازگشت</a>
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
