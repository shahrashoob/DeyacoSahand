@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("utility.public._search_view",["route"=>"line_product_station.packing.packing_type.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست انواع بسته بندی
{{--                        @if($allow_create)--}}
{{--                            <a class="btn btn-success"--}}
{{--                               href="{{route("line_product_station.packing.packing_type.create")}}">--}}
{{--                                <i--}}
{{--                                        class="fa fa-plus"></i> افزودن بسته بندی جدید </a>--}}
{{--                        @endif--}}
                        <a class="btn btn-success"
                           href="{{route("line_product_station.packing.packing_type_ic.index")}}">
                            <i
                                    class="fa fa-plus"></i> افزودن بسته بندی جدید از منظومه داده ای </a>
                        <a class="btn btn-success"
                           href="{{route("utility.special_license.panel.new_special_license.index",[13,0])}}"> <i
                                    class="fa fa-plus"></i> درخواست مجوز برای تعریف نوع بسته بندی جدید </a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive center">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد</th>
                                <th> عنوان</th>
                                <th></th>
                                <th>مدیریت بسته بندی <br/>در ورودی/خروجی ماشین</th>
                                <th> لایه های بسته بندی</th>
                                <td>وضعیت</td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.packing.packing_type.edit",$item)}}">
                                            {{$item->getCode()}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.packing.packing_type.edit",$item)}}">
                                            {{$item->caption}}
                                        </a>
                                    </td>
                                    <td>
                                        <a
                                                href="{{route("line_product_station.packing.packing_type_ic.edit",$item)}}">
                                            <i class="fa fa-undo"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.packing.packing_type.change_machine_type",$item)}}"
                                           class="text-primary"><i class="fas fa-retweet"></i> </a>
                                    </td>
                                    <td>


                                        @foreach($item->layers as $layer)
                                            لایه {{$layer->layer_code}}:
                                            {{--                                        نوع بسته بندی:--}}
                                            {{--                                       (  {{$layer->id}})--}}
                                            @if($layer->layer_code == 1 && count($item->layers) > 1)
                                                {{$item->first_packing_type->code??"***"}}
                                                -  {{$item->first_packing_type->caption??"*********"}}
                                            @else
                                                {{$layer->carrier_type->id??""}}
                                                - {{$layer->carrier_type->caption??"فاقد حامل"}}
                                            @endif
{{--                                            <a href="{{route("line_product_station.packing.packing_type.remove_layer",[$item,$layer->layer_code])}}"--}}
{{--                                               onclick="return confirm('آیا از حذف اطمینان دارید؟')"--}}
{{--                                               class="text-danger"><i class="fa fa-trash"></i> </a>--}}

                                            ,
                                        @endforeach
{{--                                        @if($item->layers()->count() <2)--}}
{{--                                            <a href="{{route("line_product_station.packing.packing_type.add_layer",$item)}}"--}}
{{--                                               class="text-success"><i class="fa fa-plus"></i> </a>--}}
{{--                                        @endif--}}
                                    </td>

                                    <td>{{$item->active_status->caption??""}}</td>

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
