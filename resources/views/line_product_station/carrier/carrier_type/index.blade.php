@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("utility.public._search_view",["route"=>"line_product_station.carrier.carrier_type.index"])

            <div class="card">
                <div class="card-header">
                    <h5>لیست انواع حامل ها
                    </h5>
{{--                        @if($allow_create)--}}
{{--                            <a class="btn btn-outline-success" href="{{route("line_product_station.carrier.carrier_type.create")}}"> <i--}}
{{--                                    class="fa fa-plus"></i> افزودن نوع حامل جدید </a>--}}
{{--                        @endif--}}
                            <a class="btn btn-outline-success" href="{{route("line_product_station.carrier.carrier_type_ic.index")}}"> <i
                                        class="fa fa-plus"></i> افزودن حامل جدید از منظومه داده ای </a>
                        <a class="btn btn-outline-success" href="{{route("utility.special_license.panel.new_special_license.index",[12,0])}}"> <i
                                    class="fa fa-plus"></i> درخواست مجوز برای تعریف نوع حامل جدید </a>

                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> کد نوع حامل</th>
                                <th> عنوان </th>
                                <th>بروز رسانی از
                                <br/>
                                    منظومه داده ای
                                </th>
                                <th>گروه حامل</th>
                                <th>حامل ها</th>
                                <th>واحد سنجش <br/> کالای حامل</th>
                                <th>میانگین وزن</th>
                                <th>حداقل باند</th>
                                <th>حداکثر باند</th>
                                <th>حداقل ظرفیت <br/>هر باند</th>
                                <th>حداکثر ظرفیت <br/>هر باند</th>
                                <th> در انبار <br/> قرار می گیرد؟</th>
                                <th>  قابلیت شماره <br/>گذاری دارد؟</th>
                                <th>سیستم می تواند <br/>حامل جدید تعریف کند</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        {{$item->id}}
                                    </td>
                                    <td>
{{--                                        <a href="{{route("line_product_station.carrier.carrier_type.edit",$item)}}" >--}}
                                            {{$item->caption}}
{{--                                        </a>--}}
                                    </td>
                                    <td>
                                        <a
                                                href="{{route("line_product_station.carrier.carrier_type_ic.edit",$item)}}">
                                            <i class="fa fa-undo"></i>
                                        </a>
                                    </td>
                                    <td>{{$item->carrier_group->caption??""}}</td>
                                    <td>
                                        @if($item->has_number_ability)
                                        <a href="{{route("line_product_station.carrier.carrier_type.carrier_list",$item)}}" >
                                            {{$item->carriers()->count()}} حامل
                                        </a>
                                        <a class="text-success" href="{{route("line_product_station.carrier.carrier_type.create_carrier",$item)}}" >
                                            <i class="fa fa-plus-circle "></i> افزودن
                                        </a>
                                        @endif
                                    </td>
                                    <td>{{$item->unit->caption??""}}</td>
                                    <td>{{$item->average_weight??""}}</td>
                                    <td>{{$item->min_band_number}}</td>
                                    <td>{{$item->max_band_number}}</td>
                                    <td>{{$item->min_band_capacity}}</td>
                                    <td>{{$item->max_band_capacity}}</td>
                                    <td>{!! $item->placed_in_warehouse?"<i class='fa fa-check'></i>" :""!!}</td>
                                    <td>{!! $item->has_number_ability?"<i class='fa fa-check'></i>":"" !!}</td>
                                    <td>{!! $item->system_can_define_new_carrier?"<i class='fa fa-check'></i>":"" !!}</td>


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
