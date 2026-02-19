@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-6">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5> لیست طبقه بندی های <b> {{$goods_kind->caption}}</b>
                        <a class="btn btn-success" href="{{route("line_product_station.goods_kind.classification.create",$goods_kind)}}"> <i
                                class="fa fa-plus"></i> افزودن  طبقه بندی جدید </a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> عنوان طبقه بندی</th>
                                <th> نوع  طبقه بندی</th>
                                <th>دسته ها</th>
                                <td></td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($goods_kind->classification as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.goods_kind.classification.edit",$item)}}">{{$item->caption}}</a>
                                    </td>
                                    <td>
                                        @if($item->classification_type->id == 1)
                                            <i class="fa fa-check "></i>
                                            {{$item->classification_type->caption}}
                                            @else
                                        <a href="{{route("line_product_station.goods_kind.classification.change_classification_type",$item)}}" onclick="return confirm('آیا از تغییر نوع طبقه بندی از فرعی به اصلی اطمینان دارید؟')">{{$item->classification_type->caption??"***"}}</a>
                                   @endif
                                    </td>
                                    <td >
                                        <a href="{{route("line_product_station.goods_kind.classification.option.index",$item)}}"> {{$item->classification_option->count()}}
                                            دسته  </a>

                                    </td>
                                    <td>
                                        <a class="text-danger" href="{{route("line_product_station.goods_kind.classification.destroy",$item)}}" onclick="confirm('آیا از حذف اطمینان دارید؟')"><fa class="fa fa-trash"></fa> </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <a class="btn btn-outline-dark" href="{{route("line_product_station.goods_kind.index",$goods_kind)}}">بازگشت</a>

                </div>

            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
