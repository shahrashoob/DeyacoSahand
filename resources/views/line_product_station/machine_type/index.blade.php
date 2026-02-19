@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست گروه های ماشین در ایستگاه
                        <b>
                            {{ $station->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> کد</th>
                                <th> گروه ماشین</th>
                                <th>تعداد ماشین</th>
                                <th>نقص های <br/>گروه ماشین</th>
                                <th>انبارک <br/>گروه ماشین</th>
                                <th>کانال تولیدهای <br/>گروه ماشین</th>
                                <th>تعداد سکشن</th>
                                <th>تعداد چشمه</th>
                                <th>کد موثر لات </th>
                                <th>تعداد  ورودی</th>
                                <th>تعداد باند خروجی</th>
                                <th>ماژول های ماشین</th>
                                <th>وضعیت گروه</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->getCode()}}

                                    </td>
                                    <td>
                                        <a href="{{ $item->station->active_status_id == 1200?route("line_product_station.machine_type.edit",$item):""}}">{{$item->caption}}</a>

                                    </td>

                                    <td>
                                        <a href="{{route("line_product_station.machine.index",$item)}}"> {{$item->machine->where("active_status_id",1200)->count()}}
                                            ماشین فعال </a>
                                        @if($item->active_status_id == 1200)
                                            <a class="text-success"
                                               href="{{route("line_product_station.machine.create",$item)}}"><i
                                                    class="fa fa-plus-circle"></i>  </a>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{route("line_product_station.machine_type.machine_fault.index",$item)}}"> {{$item->machine_fault->count()}}
                                            نقص </a>

                                    </td>

                                    <td>

                                        <a href="{{route("line_product_station.machine_type.warehouse_index",$item)}}">
                                            {{$item->warehouses->count()}}
                                            انبارک</a>
                                        @if($item->active_status_id == 1200)
                                            <a class="text-success"
                                               href="{{route("line_product_station.machine_type.warehouse_create",$item)}}"><i
                                                    class="fa fa-plus-circle"></i>  </a>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.machine_type.production_channel.index",$item)}}"> {{$item->production_channel_types->count()}}
                                            نوع کانال تولید </a>

                                    </td>
                                    <td>{{$item->section_number}}</td>
                                    <td>{{$item->position_number}}</td>
                                    <td>{{$item->lot_effective_code}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.machine_type.input_band.index",$item)}}">
                                            {{$item->input_bands()->count()}}  ورودی
                                        </a>
                                        @if($item->active_status_id == 1200)
                                            <a class="text-success"
                                               href="{{route("line_product_station.machine_type.input_band.create",$item)}}"><i
                                                    class="fa fa-plus-circle"></i>  </a>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.machine_type.output_band.index",$item)}}">
                                            {{$item->output_bands()->count()}}  خروجی
                                        </a>
                                        @if($item->active_status_id == 1200)
                                            <a class="text-success"
                                               href="{{route("line_product_station.machine_type.output_band.create",$item)}}"><i
                                                    class="fa fa-plus-circle"></i>  </a>
                                        @endif
                                    </td>
                                    <td>
                                        {{$item->machine_module_type->caption??""}}
                                        <a class=""
                                             href="{{route("line_product_station.machine_status.index",[$item,$item->machine_module_type])}}">
                                            <i class="fa fa-edit"></i>
                                            {{$item->machine_status->count()}} وضعیت   </a>
                                        <a class=" text-info"
                                           href="{{route("line_product_station.machine.machine_module_type.index",[$item,$item->machine_module_type])}}">
                                            <i class="fa fa-cog"></i> تنظیمات
                                        </a>
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
