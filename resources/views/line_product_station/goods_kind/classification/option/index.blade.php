@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-6">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5> لیست دسته بندی های طبقه  <b> {{$goods_kind_classification->caption}}</b>
                        <a class="btn btn-success" href="{{route("line_product_station.goods_kind.classification.option.create",$goods_kind_classification)}}"> <i
                                class="fa fa-plus"></i> افزودن  دسته جدید </a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> عنوان دسته </th>
                                <td></td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($goods_kind_classification->classification_option as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.goods_kind.classification.option.edit",$item)}}">{{$item->caption}}</a>
                                    </td>

                                    <td>
                                        <a class="text-danger" href="{{route("line_product_station.goods_kind.classification.option.destroy",$item)}}" onclick="confirm('آیا از حذف اطمینان دارید؟')"><fa class="fa fa-trash"></fa> </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <a class="btn btn-outline-dark" href="{{route("line_product_station.goods_kind.classification.index",$goods_kind_classification->goods_kind)}}">بازگشت</a>

                </div>

            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
