@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>مدیریت
                        <b>
                            خروجی ها
                        </b>
                        از گروه ماشین
                        <b>
                            {{$machine_type->caption}}
                        </b>
                        <a class="btn btn-success"
                           href="{{route("line_product_station.machine_type.output_band.create",$machine_type)}}"><i
                                class="fa fa-plus-circle"></i> افزودن خروجی جدید </a>
                    </h5>
                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> کد</th>
                                <th>نام باند خروجی</th>
                                <th>رسته کالایی</th>
                                <th> بسته بندی های مجاز</th>
                                <th>تعداد خط خروجی</th>
                                <th>وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($machine_type->output_bands as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->code}}

                                    </td>
                                    <td>
                                        <a href="{{ $item->machine_type->active_status_id == 1200?route("line_product_station.machine_type.output_band.edit",[$machine_type,$item]):""}}">{{$item->caption}}</a>

                                    </td>
                                    <td>
                                        @foreach($item->goods_kinds as $goods_kind_item)

                                            <a href="{{route("line_product_station.machine_type.output_band.calculation_method",[$machine_type,$item,$goods_kind_item])}}">
                                                {{$goods_kind_item->goods_kind->caption}}
                                            </a> ,
                                        @endforeach
                                            <a class="text-success"
                                               href="{{route("line_product_station.machine_type.output_band.add_goods_kind",$item)}}"><i
                                                    class="fa fa-plus-circle"></i>  </a>
                                    </td>
                                    <td>
                                        @foreach($item->goods_kinds as $goods_kind_item)
                                            @foreach($goods_kind_item->goods_kind->packing_type as $packing_type)
                                                @if($item->has_product_type_permission($packing_type->id))
                                                    {{$packing_type->caption}}<br/>
                                                @endif
                                            @endforeach
                                        @endforeach

                                    </td>


                                    <td>
                                        {{$item->output_line_number}}
                                    </td>
                                    <td>
                                        {{($item->active_status->caption??"")}}

                                    </td>
                                </tr>

                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
            <div>
                <a href="{{route("line_product_station.machine_type.index",$machine_type->station_id)}}"
                   class="btn btn-outline-dark">بازگشت</a>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
